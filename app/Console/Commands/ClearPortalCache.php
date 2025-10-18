<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class ClearPortalCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all portal caches to refresh data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing application cache...');
        Cache::flush();
        
        $this->info('Clearing config cache...');
        Artisan::call('config:clear');
        
        $this->info('Clearing route cache...');
        Artisan::call('route:clear');
        
        $this->info('Clearing view cache...');
        Artisan::call('view:clear');
        
        $this->info('All caches cleared successfully!');
    }
}