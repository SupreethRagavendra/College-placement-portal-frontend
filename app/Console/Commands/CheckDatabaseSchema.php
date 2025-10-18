<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckDatabaseSchema extends Command
{
    protected $signature = 'check:database-schema';
    protected $description = 'Check the actual database schema for questions table';

    public function handle()
    {
        $this->info('Checking database schema for questions table...');
        
        try {
            // Check if questions table exists
            if (!Schema::hasTable('questions')) {
                $this->error('Questions table does not exist!');
                return 1;
            }
            
            $this->info('Questions table exists.');
            
            // Get column information
            $columns = Schema::getColumnListing('questions');
            $this->info('Columns in questions table:');
            
            foreach ($columns as $column) {
                $this->info("  - {$column}");
            }
            
            // Try to get more detailed column info using raw SQL
            $this->info('\nDetailed column information:');
            
            try {
                $columnDetails = DB::select(
                    "SELECT column_name, data_type, is_nullable, column_default 
                     FROM information_schema.columns 
                     WHERE table_name = 'questions' 
                     ORDER BY ordinal_position"
                );
                
                foreach ($columnDetails as $detail) {
                    $nullable = $detail->is_nullable === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $detail->column_default ? "DEFAULT {$detail->column_default}" : '';
                    $this->info("  {$detail->column_name}: {$detail->data_type} {$nullable} {$default}");
                }
                
            } catch (\Exception $e) {
                $this->error('Could not get detailed column info: ' . $e->getMessage());
            }
            
            // Test a simple query
            $this->info('\nTesting simple query...');
            $count = DB::table('questions')->count();
            $this->info("Total questions in database: {$count}");
            
            if ($count > 0) {
                $sample = DB::table('questions')->first();
                $this->info('Sample question data:');
                foreach ((array)$sample as $key => $value) {
                    $displayValue = is_string($value) ? substr($value, 0, 50) . '...' : $value;
                    $this->info("  {$key}: {$displayValue}");
                }
            }
            
        } catch (\Exception $e) {
            $this->error('Error checking database schema:');
            $this->error($e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
