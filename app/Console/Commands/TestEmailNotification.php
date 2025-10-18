<?php

namespace App\Console\Commands;

use App\Mail\StudentStatusMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email : The email address to test} {status=approved : The status (approved or rejected)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notification system by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $status = $this->argument('status');

        if (!in_array($status, ['approved', 'rejected'])) {
            $this->error('Status must be either "approved" or "rejected"');
            return 1;
        }

        $this->info("Testing {$status} email to: {$email}");

        try {
            // Test Laravel Mail configuration
            $mailConfig = config('mail');
            $this->info("Current mail configuration:");
            $this->info("  MAILER: " . ($mailConfig['default'] ?? 'not set'));
            $this->info("  HOST: " . ($mailConfig['mailers']['smtp']['host'] ?? 'not set'));
            $this->info("  PORT: " . ($mailConfig['mailers']['smtp']['port'] ?? 'not set'));

            // Send test email
            $studentName = 'Test Student';
            $collegeName = config('app.name', 'College Placement Portal');
            $rejectionReason = $status === 'rejected' ? 'This is a test rejection reason' : null;

            Mail::to($email, $studentName)->send(new StudentStatusMail(
                $studentName,
                $status,
                $rejectionReason,
                $collegeName
            ));

            $this->info("✅ Test email sent successfully!");
            Log::info("Test email sent successfully", [
                'to' => $email,
                'status' => $status,
                'timestamp' => now()
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email: " . $e->getMessage());
            Log::error("Test email failed", [
                'to' => $email,
                'status' => $status,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ]);

            return 1;
        }
    }
}