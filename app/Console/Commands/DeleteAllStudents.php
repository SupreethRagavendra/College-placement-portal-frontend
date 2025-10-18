<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SupabaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteAllStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:delete-all {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all student records from the database and Supabase';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗑️  Student Data Deletion Tool');
        $this->info('==============================');

        // Count students
        $studentCount = User::where('role', 'student')->count();
        
        if ($studentCount === 0) {
            $this->info('✅ No students found in the database.');
            return 0;
        }

        $this->warn("⚠️  Found {$studentCount} student(s) in the database.");

        // Show student details
        $students = User::where('role', 'student')->get(['id', 'name', 'email', 'status', 'created_at']);
        
        $this->table(
            ['ID', 'Name', 'Email', 'Status', 'Created At'],
            $students->map(function ($student) {
                return [
                    $student->id,
                    $student->name,
                    $student->email,
                    $student->status,
                    $student->created_at->format('Y-m-d H:i:s')
                ];
            })
        );

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete ALL student data? This action cannot be undone.')) {
                $this->info('❌ Operation cancelled.');
                return 0;
            }

            if (!$this->confirm('This will delete students from both local database AND Supabase. Continue?')) {
                $this->info('❌ Operation cancelled.');
                return 0;
            }
        }

        $this->info('🔄 Starting deletion process...');

        $deletedCount = 0;
        $errors = [];

        DB::beginTransaction();
        
        try {
            foreach ($students as $student) {
                try {
                    // Delete from Supabase if they have a supabase_id
                    if ($student->supabase_id) {
                        try {
                            $supabaseService = app(SupabaseService::class);
                            $supabaseService->deleteUser($student->supabase_id);
                            $this->line("✅ Deleted from Supabase: {$student->name}");
                        } catch (\Exception $e) {
                            $this->warn("⚠️  Failed to delete from Supabase: {$student->name} - {$e->getMessage()}");
                            $errors[] = "Supabase deletion failed for {$student->name}: {$e->getMessage()}";
                        }
                    }

                    // Delete from local database
                    $student->delete();
                    $deletedCount++;
                    $this->line("✅ Deleted from database: {$student->name}");

                } catch (\Exception $e) {
                    $this->error("❌ Failed to delete {$student->name}: {$e->getMessage()}");
                    $errors[] = "Database deletion failed for {$student->name}: {$e->getMessage()}";
                }
            }

            DB::commit();
            
            $this->info("🎉 Successfully deleted {$deletedCount} student(s)!");
            
            if (!empty($errors)) {
                $this->warn("⚠️  Some errors occurred:");
                foreach ($errors as $error) {
                    $this->line("   - {$error}");
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Transaction failed: {$e->getMessage()}");
            return 1;
        }

        // Show final statistics
        $remainingStudents = User::where('role', 'student')->count();
        $this->info("📊 Remaining students in database: {$remainingStudents}");

        return 0;
    }
}