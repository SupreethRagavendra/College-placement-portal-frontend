<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessEmailQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:process-emails {--max-jobs=50 : Maximum number of jobs to process} {--sleep=3 : Sleep time between jobs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process email queue for sending notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $maxJobs = $this->option('max-jobs');
        $sleep = $this->option('sleep');
        
        $this->info("Processing email queue (max: {$maxJobs} jobs, sleep: {$sleep}s)...");
        
        // Process queue jobs
        Artisan::call('queue:work', [
            '--max-jobs' => $maxJobs,
            '--sleep' => $sleep,
            '--quiet' => true,
            '--stop-when-empty' => true
        ]);
        
        $this->info('Email queue processing completed.');
    }
}