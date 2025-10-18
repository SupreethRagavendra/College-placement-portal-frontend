<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupTestCommands extends Command
{
    protected $signature = 'cleanup:test-commands';
    protected $description = 'Remove test command files';

    public function handle()
    {
        $testCommands = [
            'TestAssessmentSystem.php',
            'TestStudentAssessmentAccess.php', 
            'TestAssessmentToggle.php',
            'TestAssessmentQuestionRelationship.php',
            'CleanupTestCommands.php'
        ];
        
        $commandsPath = app_path('Console/Commands');
        
        foreach ($testCommands as $command) {
            $filePath = $commandsPath . '/' . $command;
            if (File::exists($filePath)) {
                File::delete($filePath);
                $this->info("Deleted: {$command}");
            }
        }
        
        $this->info('Test command cleanup completed!');
        return 0;
    }
}
