<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\StudentResult;
use App\Services\SupabaseService;
use App\Services\EmailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    protected $supabaseService;
    protected $emailService;

    public function __construct(SupabaseService $supabaseService, EmailNotificationService $emailService)
    {
        $this->supabaseService = $supabaseService;
        $this->emailService = $emailService;
    }

    /**
     * Show admin dashboard
     */
    public function dashboard(): View
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        // Cache dashboard statistics for 10 minutes - AGGRESSIVE CACHING for speed
        $stats = Cache::remember('admin_dashboard_stats', 600, function() {
            return [
                'total_students' => User::where('role', 'student')->count(),
                'approved_students' => User::approved()->count(),
                'pending_students' => User::pending()->count(),
                'rejected_students' => User::rejected()->count(),
                'total_assessments' => Assessment::count(),
                'active_assessments' => Assessment::where('is_active', true)->count(),
                'total_questions' => Question::count(),
                'total_attempts' => StudentResult::count(),
            ];
        });

        // Cache average score calculation for 10 minutes - AGGRESSIVE CACHING for speed
        $stats['average_score'] = Cache::remember('admin_dashboard_avg_score', 600, function() {
            $averageScore = StudentResult::selectRaw('AVG((score / total_questions) * 100) as avg_percentage')
                ->value('avg_percentage');
            return round($averageScore ?? 0, 2);
        });

        // Cache recent assessments for 10 minutes - AGGRESSIVE CACHING for speed
        $recentAssessments = Cache::remember('admin_recent_assessments', 600, function() {
            return Assessment::withCount(['questions', 'studentResults'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        });

        // Cache assessment analytics for 5 minutes
        $assessmentAnalytics = Cache::remember('admin_assessment_analytics', 300, function() {
            return Assessment::select('id', 'title', 'category')
            ->withCount('studentResults')
            ->get()
            ->map(function($assessment) {
                $results = $assessment->studentResults()->select('assessment_id', 'score', 'total_questions')->get();
                return [
                    'id' => $assessment->id,
                    'title' => $assessment->title,
                    'category' => $assessment->category,
                    'attempts' => $assessment->student_results_count,
                    'avg_score' => round($results->avg('score') ?? 0, 2),
                    'avg_percentage' => $results->count() > 0 
                        ? round($results->avg(function($result) {
                            return $result->total_questions > 0 ? ($result->score / $result->total_questions) * 100 : 0;
                        }), 2) : 0,
                    'highest_score' => $results->max('score') ?? 0,
                    'lowest_score' => $results->min('score') ?? 0,
                ];
            });
        });

        // Cache top students for 5 minutes
        $topStudents = Cache::remember('admin_top_students', 300, function() {
            return StudentResult::select('student_id')
                ->selectRaw('AVG((score / total_questions) * 100) as avg_percentage')
                ->selectRaw('COUNT(*) as attempts_count')
                ->with('student:id,name,email')
                ->groupBy('student_id')
                ->havingRaw('COUNT(*) >= 1')
                ->orderBy('avg_percentage', 'desc')
                ->limit(5)
                ->get();
        });

        // Cache category performance for 5 minutes
        $categoryPerformance = Cache::remember('admin_category_performance', 300, function() {
            return Assessment::select('category')
                ->join('student_results', 'assessments.id', '=', 'student_results.assessment_id')
                ->selectRaw('category, AVG((score / total_questions) * 100) as avg_percentage, COUNT(*) as total_attempts')
                ->groupBy('category')
                ->get()
                ->map(function($item) {
                    return [
                        'category' => $item->category,
                        'avg_percentage' => round($item->avg_percentage ?? 0, 2),
                        'total_attempts' => $item->total_attempts ?? 0
                    ];
                });
        });

        // Cache pending students for 5 minutes - AGGRESSIVE CACHING for speed
        $pendingStudents = Cache::remember('admin_pending_students', 300, function() {
            return User::pending()->orderBy('created_at', 'desc')->get();
        });

        // Cache recent approvals for 10 minutes - AGGRESSIVE CACHING for speed
        $recentApprovals = Cache::remember('admin_recent_approvals', 600, function() {
            return User::approved()->orderBy('admin_approved_at', 'desc')->limit(5)->get();
        });

        return view('admin.dashboard', compact(
            'stats', 
            'pendingStudents', 
            'recentApprovals', 
            'recentAssessments', 
            'assessmentAnalytics', 
            'topStudents', 
            'categoryPerformance'
        ));
    }

    /**
     * Show pending students for approval
     */
    public function pendingStudents(): View
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $pendingStudents = User::pending()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.pending-students', compact('pendingStudents'));
    }

    /**
     * Show approved students
     */
    public function approvedStudents(): View
    {
        $approvedStudents = User::approved()
            ->orderBy('admin_approved_at', 'desc')
            ->paginate(10);

        return view('admin.approved-students', compact('approvedStudents'));
    }

    /**
     * Show rejected students
     */
    public function rejectedStudents(): View
    {
        $rejectedStudents = User::rejected()
            ->orderBy('admin_rejected_at', 'desc')
            ->paginate(10);

        return view('admin.rejected-students', compact('rejectedStudents'));
    }

    /**
     * Approve a student
     */
    public function approveStudent(Request $request, $id)
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $student = User::where('id', $id)
            ->where('role', 'student')
            ->where('is_verified', true)
            ->where('is_approved', false)
            ->whereNull('admin_rejected_at')
            ->first();

        if (!$student) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Student not found or already processed.'], 404);
            }
            return back()->withErrors(['error' => 'Student not found or already processed.']);
        }

        DB::beginTransaction();
        try {
            // Update local database
            $student->update([
                'is_approved' => true,
                'admin_approved_at' => now(),
                'status' => 'approved'
            ]);

            DB::commit();
            
            // Clear cache
            Cache::forget('admin_dashboard_stats');
            Cache::forget('admin_dashboard_avg_score');
            Cache::forget('admin_pending_students');
            Cache::forget('admin_recent_approvals');

            // Send approval email asynchronously (non-blocking)
            \Log::info("ADMIN CONTROLLER APPROVAL: About to send email", [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'student_name' => $student->name,
                'timestamp' => now()
            ]);
            
            $this->sendStatusEmailAsync($student, 'approved');
            
            \Log::info("ADMIN CONTROLLER APPROVAL: Email sending initiated", [
                'student_id' => $student->id,
                'student_email' => $student->email,
                'timestamp' => now()
            ]);

            $message = "Student {$student->name} has been approved successfully.";
            
            if ($request->expectsJson()) {
                return response()->json(['status' => 'success', 'message' => $message]);
            }
            
            return back()->with('status', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to approve student. Please try again.'], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to approve student. Please try again.']);
        }
    }

    /**
     * Reject a student
     */
    public function rejectStudent(Request $request, $id)
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500'
        ]);

        $student = User::where('id', $id)
            ->where('role', 'student')
            ->where('is_verified', true)
            ->where('is_approved', false)
            ->whereNull('admin_rejected_at')
            ->first();

        if (!$student) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Student not found or already processed.'], 404);
            }
            return back()->withErrors(['error' => 'Student not found or already processed.']);
        }

        DB::beginTransaction();
        try {
            // Mark student as rejected instead of deleting
            $student->update([
                'admin_rejected_at' => now(),
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason
            ]);

            DB::commit();
            
            // Clear cache
            Cache::forget('admin_dashboard_stats');
            Cache::forget('admin_dashboard_avg_score');
            Cache::forget('admin_pending_students');

            // Send rejection email asynchronously (non-blocking)
            $this->sendStatusEmailAsync($student, 'rejected', $request->rejection_reason);

            $message = "Student {$student->name} has been rejected.";
            
            if ($request->expectsJson()) {
                return response()->json(['status' => 'success', 'message' => $message]);
            }
            
            return back()->with('status', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to reject student. Please try again.'], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to reject student. Please try again.']);
        }
    }

    /**
     * Bulk approve students
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        $students = User::whereIn('id', $request->student_ids)
            ->where('role', 'student')
            ->where('is_verified', true)
            ->where('is_approved', false)
            ->whereNull('admin_rejected_at')
            ->get();

        if ($students->isEmpty()) {
            return back()->withErrors(['error' => 'No valid students found for approval.']);
        }

        DB::beginTransaction();
        try {
            $approvedCount = 0;
            foreach ($students as $student) {
                $student->update([
                    'is_approved' => true,
                    'admin_approved_at' => now(),
                    'status' => 'approved'
                ]);
                
                // Send approval email asynchronously for each student
                $this->sendStatusEmailAsync($student, 'approved');
                $approvedCount++;
            }

            DB::commit();
            
            // Clear cache after bulk approval
            Cache::forget('admin_dashboard_stats');
            Cache::forget('admin_dashboard_avg_score');
            Cache::forget('admin_pending_students');
            Cache::forget('admin_recent_approvals');

            return back()->with('status', "Successfully approved {$approvedCount} students.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to approve students. Please try again.']);
        }
    }

    /**
     * Bulk reject students
     */
    public function bulkReject(Request $request): RedirectResponse
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id'
        ]);

        $students = User::whereIn('id', $request->student_ids)
            ->where('role', 'student')
            ->whereNull('admin_rejected_at')
            ->get();

        if ($students->isEmpty()) {
            return back()->withErrors(['error' => 'No valid students found for rejection.']);
        }

        DB::beginTransaction();
        try {
            $rejectedCount = 0;
            foreach ($students as $student) {
                // Mark student as rejected instead of deleting
                $student->update([
                    'admin_rejected_at' => now(),
                    'status' => 'rejected'
                ]);
                
                // Send rejection email asynchronously for each student
                $this->sendStatusEmailAsync($student, 'rejected');
                $rejectedCount++;
            }

            DB::commit();
            
            // Clear cache after bulk rejection
            Cache::forget('admin_dashboard_stats');
            Cache::forget('admin_dashboard_avg_score');
            Cache::forget('admin_pending_students');

            return back()->with('status', "Successfully rejected {$rejectedCount} students.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reject students. Please try again.']);
        }
    }

    /**
     * Get student details for modal
     */
    public function getStudentDetails($id)
    {
        $student = User::where('id', $id)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json([
            'id' => $student->id,
            'name' => $student->name,
            'email' => $student->email,
            'created_at' => $student->created_at->format('M d, Y H:i'),
            'email_verified_at' => $student->email_verified_at ? $student->email_verified_at->format('M d, Y H:i') : null,
            'admin_rejected_at' => $student->admin_rejected_at ? $student->admin_rejected_at->format('M d, Y H:i') : null,
            'rejection_reason' => $student->rejection_reason,
            'status' => $student->status,
            'is_pending' => $student->isPendingApproval(),
            'is_approved' => $student->isApproved(),
            'is_rejected' => $student->isRejected(),
        ]);
    }

    /**
     * Restore a rejected student back to pending status
     */
    public function restoreStudent(Request $request, $id): RedirectResponse
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $student = User::where('id', $id)
            ->where('role', 'student')
            ->whereNotNull('admin_rejected_at')
            ->first();

        if (!$student) {
            return back()->withErrors(['error' => 'Student not found or not rejected.']);
        }

        DB::beginTransaction();
        try {
            // Restore student to pending status
            $student->update([
                'admin_rejected_at' => null,
                'rejection_reason' => null,
                'status' => 'pending',
                'is_approved' => false
            ]);

            DB::commit();
            
            // Clear cache after restoring student
            Cache::forget('admin_dashboard_stats');
            Cache::forget('admin_dashboard_avg_score');
            Cache::forget('admin_pending_students');

            return back()->with('status', "Student {$student->name} has been restored to pending status.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to restore student. Please try again.']);
        }
    }

    /**
     * Revoke access for an approved student (completely delete the record)
     */
    public function revokeStudent(Request $request, $id)
    {
        // Check if user is authenticated and is admin
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized access.'], 403);
            }
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $student = User::where('id', $id)
            ->where('role', 'student')
            ->where('is_approved', true)
            ->whereNull('admin_rejected_at')
            ->first();

        if (!$student) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Student not found or already processed.'], 404);
            }
            return back()->withErrors(['error' => 'Student not found or already processed.']);
        }

        DB::beginTransaction();
        try {
            $studentName = $student->name;
            $studentEmail = $student->email;
            $supabaseUserId = $student->supabase_user_id ?? null;
            
            // Clear cache related to this student
            Cache::forget('admin_dashboard_stats');
            Cache::forget('admin_dashboard_avg_score');
            Cache::forget('admin_recent_approvals');
            Cache::forget('admin_top_students');
            
            // Send revocation email before deleting the record
            $this->sendStatusEmailAsync($student, 'rejected', 'Access revoked by administrator');
            
            // Delete all related student data first
            \App\Models\StudentResult::where('student_id', $id)->delete();
            \App\Models\StudentAssessment::where('student_id', $id)->delete();
            \App\Models\StudentAnswer::where('student_assessment_id', function($query) use ($id) {
                $query->select('id')->from('student_assessments')->where('student_id', $id);
            })->delete();
            
            // If using Supabase authentication, also delete from Supabase
            if ($supabaseUserId) {
                try {
                    $this->supabaseService->deleteUser($supabaseUserId);
                    \Log::info("Deleted Supabase user: {$supabaseUserId} for {$studentEmail}");
                } catch (\Exception $e) {
                    \Log::warning("Failed to delete Supabase user {$supabaseUserId}: " . $e->getMessage());
                    // Continue with local deletion even if Supabase deletion fails
                }
            }
            
            // Completely delete the student record to free up the email
            $student->delete();

            DB::commit();

            $message = "Access for {$studentName} ({$studentEmail}) has been revoked and the account has been completely removed. The email is now available for new registrations.";
            
            if ($request->expectsJson()) {
                return response()->json(['status' => 'success', 'message' => $message]);
            }
            
            return back()->with('status', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to revoke student access', ['error' => $e->getMessage(), 'student_id' => $id]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Failed to revoke access. Please try again.'], 500);
            }
            
            return back()->withErrors(['error' => 'Failed to revoke access. Please try again.']);
        }
    }

    /**
     * Clear all admin dashboard caches (for debugging)
     */
    public function clearCaches(): RedirectResponse
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect()->route('login')->withErrors(['error' => 'Unauthorized access.']);
        }

        $this->clearAdminCaches();
        
        return back()->with('status', 'All admin dashboard caches have been cleared.');
    }

    /**
     * Clear all admin dashboard caches
     */
    private function clearAdminCaches()
    {
        Cache::forget('admin_dashboard_stats');
        Cache::forget('admin_dashboard_avg_score');
        Cache::forget('admin_pending_students');
        Cache::forget('admin_recent_approvals');
        Cache::forget('admin_recent_assessments');
        Cache::forget('admin_assessment_analytics');
        Cache::forget('admin_top_students');
        Cache::forget('admin_category_performance');
    }

    /**
     * Send status email asynchronously to student
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