<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SupabaseService;
use Illuminate\Console\Command;

class CleanupRevokedStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:cleanup-revoked 
                           {--email= : Specific email to clean up}
                           {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up revoked/rejected students to free up their emails for new registrations';

    protected $supabaseService;

    /**
     * Create a new command instance.
     */
    public function __construct(SupabaseService $supabaseService)
    {
        parent::__construct();
        $this->supabaseService = $supabaseService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $dryRun = $this->option('dry-run');

        $this->info('🧹 Cleaning up revoked/rejected students...');
        $this->newLine();

        // Find students to clean up
        $query = User::where('role', 'student')
            ->where(function($q) {
                $q->where('status', 'rejected')
                  ->orWhereNotNull('admin_rejected_at')
                  ->orWhere(function($q2) {
                      $q2->where('is_approved', false)
                         ->where('is_verified', false)
                         ->where('created_at', '<', now()->subDays(30)); // Old unverified accounts
                  });
            });

        if ($email) {
            $query->where('email', $email);
        }

        $studentsToCleanup = $query->get();

        if ($studentsToCleanup->isEmpty()) {
            $this->warn('No students found to clean up.');
            return 0;
        }

        $this->table(['ID', 'Name', 'Email', 'Status', 'Created', 'Rejected At'], 
            $studentsToCleanup->map(function($student) {
                return [
                    $student->id,
                    $student->name,
                    $student->email,
                    $student->status ?? 'unverified',
                    $student->created_at->format('Y-m-d H:i'),
                    $student->admin_rejected_at ? $student->admin_rejected_at->format('Y-m-d H:i') : 'N/A'
                ];
            })->toArray()
        );

        $this->newLine();

        if ($dryRun) {
            $this->info('DRY RUN: The above students would be deleted.');
            return 0;
        }

        if (!$this->confirm("Delete {$studentsToCleanup->count()} student records? This will free up their emails for new registrations.")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $deletedCount = 0;
        $failedCount = 0;

        foreach ($studentsToCleanup as $student) {
            try {
                $this->info("Deleting: {$student->name} ({$student->email})");

                // Delete related data first
                \App\Models\StudentResult::where('student_id', $student->id)->delete();

                // Delete from Supabase if applicable
                if ($student->supabase_user_id) {
                    try {
                        $this->supabaseService->deleteUser($student->supabase_user_id);
                        $this->line("  ✓ Deleted from Supabase");
                    } catch (\Exception $e) {
                        $this->warn("  ⚠ Failed to delete from Supabase: " . $e->getMessage());
                    }
                }

                // Delete from local database
                $student->delete();
                $this->line("  ✓ Deleted from local database");

                $deletedCount++;

            } catch (\Exception $e) {
                $this->error("  ✗ Failed to delete {$student->email}: " . $e->getMessage());
                $failedCount++;
            }
        }

        $this->newLine();
        $this->info("✅ Cleanup completed!");
        $this->info("   Deleted: {$deletedCount} students");
        if ($failedCount > 0) {
            $this->warn("   Failed: {$failedCount} students");
        }
        $this->info("   Their emails are now available for new registrations.");

        return 0;
    }
}
