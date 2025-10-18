<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ClearUserCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:clear-user-cache {userId? : The ID of the user to clear cache for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear user-specific caches to refresh data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        
        if ($userId) {
            // Clear cache for specific user
            $this->clearUserCache($userId);
            $this->info("Cache cleared for user ID: {$userId}");
        } else {
            // Clear all user-related caches
            $this->clearAllUserCaches();
            $this->info('All user caches cleared successfully!');
        }
    }
    
    /**
     * Clear cache for a specific user
     */
    private function clearUserCache($userId)
    {
        $keys = [
            "user_is_admin_{$userId}",
            "user_is_student_{$userId}",
            "user_is_approved_{$userId}",
            "user_can_login_{$userId}",
            "user_is_pending_{$userId}",
            "user_is_rejected_{$userId}",
            "user_dashboard_route_{$userId}",
            "user_login_status_{$userId}"
        ];
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
    
    /**
     * Clear all user-related caches
     */
    private function clearAllUserCaches()
    {
        // In a production environment, you might want to use a more sophisticated
        // approach to clear all user caches, but for now we'll just clear the application cache
        Cache::flush();
        $this->info('Clearing application cache...');
        Artisan::call('config:clear');
        $this->info('Clearing config cache...');
        Artisan::call('route:clear');
        $this->info('Clearing route cache...');
        Artisan::call('view:clear');
        $this->info('Clearing view cache...');
    }
}