<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearStudentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:clear {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quickly clear all student data from local database only';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Quick Student Data Clear');
        $this->info('==========================');

        // Count students
        $studentCount = User::where('role', 'student')->count();
        
        if ($studentCount === 0) {
            $this->info('✅ No students found in the database.');
            return 0;
        }

        $this->warn("⚠️  Found {$studentCount} student(s) to delete.");

        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Delete all student data from local database? This will NOT delete from Supabase.')) {
                $this->info('❌ Operation cancelled.');
                return 0;
            }
        }

        $this->info('🔄 Deleting student data...');

        try {
            // Delete all students from local database
            $deletedCount = User::where('role', 'student')->delete();
            
            $this->info("✅ Successfully deleted {$deletedCount} student(s) from local database!");
            $this->warn("⚠️  Note: Students are still in Supabase. Use 'students:delete-all' to delete from both.");

        } catch (\Exception $e) {
            $this->error("❌ Failed to delete students: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}