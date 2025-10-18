<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\SetupSupabaseStudentPanel::class,
        \App\Console\Commands\SetupSupabaseDbTables::class,
        \App\Console\Commands\ClearStudentData::class,
        \App\Console\Commands\DeleteAllStudents::class,
        \App\Console\Commands\TestEmailNotification::class,
        \App\Console\Commands\CleanupRevokedStudents::class,
        \App\Console\Commands\ClearPortalCache::class,
        \App\Console\Commands\ProcessEmailQueue::class,
        \App\Console\Commands\ClearUserCache::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process email queue every minute
        $schedule->command('portal:process-emails')->everyMinute();
        
        // Clear cache daily at midnight
        $schedule->command('portal:clear-cache')->dailyAt('00:00');
        
        // Cleanup revoked students daily
        $schedule->command('app:cleanup-revoked-students')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}