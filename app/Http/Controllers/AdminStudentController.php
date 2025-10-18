<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SupabaseService;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminStudentController extends Controller
{
    protected $supabaseService;
    protected $emailService;

    public function __construct(SupabaseService $supabaseService, EmailNotificationService $emailService)
    {
        $this->supabaseService = $supabaseService;
        $this->emailService = $emailService;
    }
    /**
     * Show pending students page
     */
    public function pending(): View
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $students = User::pending()
            ->paginate(10);

        return view('admin.students.pending', compact('students'));
    }

    /**
     * Approve student (sets is_approved = true)
     */
    public function approve($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $student = User::where('id', $id)
            ->where('role', 'student')
            ->where('is_verified', true)
            ->where('is_approved', false)
            ->first();
            
        if (!$student) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Student not found or already processed'], 404);
            }
            return back()->withErrors(['error' => 'Student not found or already processed']);
        }

        DB::beginTransaction();
        try {
            $studentName = $student->name;
            $student->update([
                'is_approved' => true,
                'admin_approved_at' => now(),
                'status' => 'active'
            ]);
            DB::commit();
            
            // Clear cache after approval
            Cache::forget('admin_dashboard_stats');
            Cache::forget('admin_dashboard_avg_score');
            Cache::forget('admin_pending_students');
            Cache::forget('admin_recent_approvals');
            
            // Send approval email asynchronously (non-blocking)
            \Log::info("ADMIN APPROVAL: About to send email", [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'student_name' => $student->name,
                'timestamp' => now()
            ]);
            
            $this->sendStatusEmailAsync($student, 'approved');
            
            \Log::info("ADMIN APPROVAL: Email sending initiated", [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'timestamp' => now()
            ]);
            
            if (request()->ajax()) {
                return response()->json(['success' => "Student {$studentName} has been approved."]);
            }
            return back()->with('status', "Student {$studentName} has been approved.");
        } catch (\Exception $e) {
            DB::rollBack();
            if (request()->ajax()) {
                return response()->json(['error' => 'Failed to approve student'], 500);
            }
            return back()->withErrors(['error' => 'Failed to approve student']);
        }
    }

    /**
     * Reject student
     */
    public function reject(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $student = User::where('id', $id)
            ->where('role', 'student')
            ->whereNull('admin_rejected_at')
            ->first();
            
        if (!$student) {
            return back()->withErrors(['error' => 'Student not found or already processed']);
        }

        DB::beginTransaction();
        try {
            $studentName = $student->name;
            $student->update([
                'admin_rejected_at' => now(),
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason
            ]);
            DB::commit();
            
            // Clear cache after rejection
            Cache::forget('admin_dashboard_stats');
            Cache::forget('admin_dashboard_avg_score');
            Cache::forget('admin_pending_students');
            
            // Send rejection email asynchronously (non-blocking)
            \Log::info("ADMIN REJECTION: About to send email", [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'student_name' => $student->name,
                'rejection_reason' => $request->rejection_reason,
                'timestamp' => now()
            ]);
            
            $this->sendStatusEmailAsync($student, 'rejected', $request->rejection_reason);
            
            \Log::info("ADMIN REJECTION: Email sending initiated", [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'timestamp' => now()
            ]);
            
            return back()->with('status', "Student {$studentName} has been rejected.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reject student']);
        }
    }

    /**
     * Send status email asynchronously
     */
    private function sendStatusEmailAsync(User $student, string $status, ?string $rejectionReason = null): void
    {
        try {
            // Log the email sending attempt
            \Log::info("Sending status email", [
                'student_email' => $student->email,
                'student_name' => $student->name,
                'status' => $status,
                'rejection_reason' => $rejectionReason,
                'timestamp' => now()
            ]);

            // Send email notification using EmailNotificationService first (preferred method)
            $emailSent = $this->emailService->sendStatusEmail(
                $student->email,
                $student->name,
                $status,
                $rejectionReason
            );

            if ($emailSent) {
                \Log::info("Status email sent successfully via EmailNotificationService", [
                    'student_email' => $student->email,
                    'status' => $status,
                    'timestamp' => now()
                ]);
                return;
            }

            // Fallback: Try Supabase service
            \Log::warning("EmailNotificationService failed, trying Supabase service", [
                'student_email' => $student->email,
                'status' => $status
            ]);

            $promise = $this->supabaseService->sendStatusEmailAsync(
                $student->email,
                $student->name,
                $status,
                $rejectionReason
            );

            // Handle the promise completion (optional - for logging)
            if ($promise) {
                $promise->then(
                    function ($response) use ($student, $status) {
                        \Log::info("Status email sent successfully via Supabase", [
                            'student_email' => $student->email,
                            'status' => $status,
                            'timestamp' => now()
                        ]);
                    },
                    function ($exception) use ($student, $status) {
                        \Log::error("Failed to send status email via Supabase", [
                            'student_email' => $student->email,
                            'status' => $status,
                            'error' => $exception->getMessage(),
                            'timestamp' => now()
                        ]);
                    }
                );
            } else {
                \Log::warning("Supabase service returned null promise", [
                    'student_email' => $student->email,
                    'status' => $status
                ]);
            }

        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            \Log::error("Error initiating status email", [
                'student_email' => $student->email,
                'status' => $status,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);
        }
    }
}