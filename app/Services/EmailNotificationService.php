<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Queue;

class EmailNotificationService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false, // For development only
        ]);
    }

    /**
     * Send status email notification to student asynchronously
     */
    public function sendStatusEmail(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): bool
    {
        Log::info('EMAIL SERVICE: Starting to send status email', [
                                'student_email' => $studentEmail,
            'student_name' => $studentName,
                                'status' => $status,
                                'timestamp' => now()
                            ]);
        
        try {
            // Send email immediately using Laravel Mail (synchronous)
            $studentEmailResult = $this->sendViaLaravelMail($studentEmail, $studentName, $status, $rejectionReason);
                
                if ($studentEmailResult) {
                Log::info('✅ EMAIL SERVICE: Student email sent successfully via Laravel Mail', [
                        'student_email' => $studentEmail,
                        'student_name' => $studentName,
                        'status' => $status,
                        'timestamp' => now()
                    ]);

                // Also send admin notification for tracking (async to avoid blocking)
                $this->sendAdminNotificationAsync($studentEmail, $studentName, $status, $rejectionReason);

                return true;
                } else {
                Log::error('❌ EMAIL SERVICE: Failed to send email to student', [
                        'student_email' => $studentEmail,
                        'student_name' => $studentName,
                        'status' => $status,
                        'timestamp' => now()
                    ]);
                return false;
                }

            } catch (\Exception $e) {
                Log::error('Failed to send status email notification', [
                    'student_email' => $studentEmail,
                    'student_name' => $studentName,
                    'status' => $status,
                    'error' => $e->getMessage(),
                    'timestamp' => now()
                ]);

            // Try fallback method if Laravel Mail fails
            try {
                return $this->sendViaFallback($studentEmail, $studentName, $status, $rejectionReason);
            } catch (\Exception $fallbackError) {
                Log::error('Fallback email method also failed', [
                    'student_email' => $studentEmail,
                    'status' => $status,
                    'fallback_error' => $fallbackError->getMessage(),
                    'timestamp' => now()
                ]);
                return false;
            }
        }
    }



    /**
     * Send admin notification about student status change
     */
    private function sendAdminNotification(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): bool
    {
        try {
            $collegeName = config('app.name', 'College Placement Portal');
            $currentTime = now()->format('Y-m-d H:i:s');
            
            $subject = $status === 'approved' 
                ? "🎉 STUDENT APPROVED: {$studentName} ({$studentEmail})"
                : "❌ STUDENT REJECTED: {$studentName} ({$studentEmail})";
            
            $message = "STUDENT STATUS CHANGE NOTIFICATION\n\n" .
                      "A student status has been updated in the {$collegeName}.\n\n" .
                      "Student Details:\n" .
                      "- Name: {$studentName}\n" .
                      "- Email: {$studentEmail}\n" .
                      "- Status: " . strtoupper($status) . "\n" .
                      "- Time: {$currentTime}\n";
            
            if ($rejectionReason) {
                $message .= "- Rejection Reason: {$rejectionReason}\n";
            }
            
            $message .= "\n" . str_repeat("=", 50) . "\n";
            $message .= "EMAIL CONTENT FOR STUDENT NOTIFICATION:\n";
            $message .= str_repeat("=", 50) . "\n\n";
            
            if ($status === 'approved') {
                $message .= "Subject: 🎉 Account Approved - Welcome to {$collegeName}!\n\n";
                $message .= "Dear {$studentName},\n\n";
                $message .= "Your account has been approved for the College Placement Portal!\n\n";
                $message .= "What's Next?\n";
                $message .= "- Log in to access the portal\n";
                $message .= "- Complete your profile\n";
                $message .= "- Browse placement opportunities\n";
                $message .= "- Take practice assessments\n\n";
                $message .= "Portal URL: " . config('app.url', 'http://localhost:8000') . "/login\n\n";
                $message .= "Best regards,\n{$collegeName} Team";
            } else {
                $message .= "Subject: Application Status Update - {$collegeName}\n\n";
                $message .= "Dear {$studentName},\n\n";
                $message .= "Thank you for your interest in {$collegeName}. After careful review, your account registration has been not approved at this time.\n\n";
                if ($rejectionReason) {
                    $message .= "Reason for Decision: {$rejectionReason}\n\n";
                }
                $message .= "You may reapply after addressing any concerns mentioned.\n\n";
                $message .= "For questions, contact us at: supreethvennila@gmail.com\n\n";
                $message .= "Best regards,\n{$collegeName} Admissions Team";
            }
            
            $message .= "\n\n" . str_repeat("=", 50) . "\n";
            $message .= "This notification was generated automatically when an admin {$status} the student.";
            
            // Send via Formspree to admin email
            $formspreeEndpoint = 'https://formspree.io/f/xanpndqw';
            
            $response = $this->client->post($formspreeEndpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => [
                    'email' => 'noreply@collegeportal.com',
                    'name' => 'College Portal Notification System',
                    'subject' => $subject,
                    'message' => $message,
                    '_replyto' => 'noreply@collegeportal.com',
                    'notification_type' => 'student_status_change',
                    'student_email' => $studentEmail,
                    'student_name' => $studentName,
                    'status' => $status
                ],
                'timeout' => 30
            ]);
            
            if ($response->getStatusCode() === 200) {
                Log::info('Formspree admin notification sent successfully', [
                    'student_email' => $studentEmail,
                    'status' => $status,
                    'admin_email' => 'supreethvennila@gmail.com',
                    'subject' => $subject,
                    'response' => json_decode($response->getBody()->getContents(), true)
                ]);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Failed to send admin notification', [
                'error' => $e->getMessage(),
                'student_email' => $studentEmail,
                'status' => $status
            ]);
            return false;
        }
    }


    /**
     * Send email via Laravel Mail system (SMTP/Gmail/etc) using Mailable class
     */
    private function sendViaLaravelMail(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): bool
    {
        try {
            $collegeName = config('app.name', 'College Placement Portal');

            // Use the StudentStatusMail mailable class for proper email sending
            Mail::to($studentEmail, $studentName)->send(new \App\Mail\StudentStatusMail(
                $studentName,
                $status,
                $rejectionReason,
                $collegeName
            ));

            Log::info('Laravel Mail sent successfully via StudentStatusMail', [
                'to' => $studentEmail,
                'status' => $status,
                'timestamp' => now()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Laravel Mail failed', [
                'error' => $e->getMessage(),
                'student_email' => $studentEmail,
                'status' => $status
            ]);
            return false;
        }
    }

    /**
     * Send admin notification asynchronously
     */
    private function sendAdminNotificationAsync(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): void
    {
        try {
            Queue::push(function() use ($studentEmail, $studentName, $status, $rejectionReason) {
                $this->sendAdminNotification($studentEmail, $studentName, $status, $rejectionReason);
            });
        } catch (\Exception $e) {
            Log::error('Failed to queue admin notification', [
                'error' => $e->getMessage(),
                'student_email' => $studentEmail,
                'status' => $status
            ]);
        }
    }

    /**
     * Fallback email method using direct HTTP request to a webhook service
     */
    private function sendViaFallback(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): bool
    {
        try {
            $collegeName = config('app.name', 'College Placement Portal');
            
            // Use a webhook service like Webhook.site or similar for testing
            // In production, this could be replaced with a proper email service API
            $webhookUrl = 'https://webhook.site/your-webhook-id'; // Replace with actual webhook URL
            
            $emailData = [
                'to' => $studentEmail,
                'from' => config('mail.from.address', 'noreply@collegeportal.com'),
                'from_name' => $collegeName,
                'subject' => $status === 'approved' 
                    ? "🎉 Account Approved - Welcome to {$collegeName}!"
                    : "Application Status Update - {$collegeName}",
                'html_content' => $status === 'approved'
                    ? $this->generateApprovedEmailHTML($studentName, $collegeName)
                    : $this->generateRejectedEmailHTML($studentName, $collegeName, $rejectionReason),
                'text_content' => $status === 'approved'
                    ? $this->generateApprovedEmailText($studentName, $collegeName)
                    : $this->generateRejectedEmailText($studentName, $collegeName, $rejectionReason),
                'status' => $status,
                'student_name' => $studentName,
                'rejection_reason' => $rejectionReason,
                'timestamp' => now()->toISOString()
            ];
            
            $response = $this->client->post($webhookUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'json' => $emailData,
                'timeout' => 30
            ]);
            
            if ($response->getStatusCode() === 200) {
                Log::info('Fallback email sent successfully via webhook', [
                    'student_email' => $studentEmail,
                    'status' => $status,
                    'webhook_url' => $webhookUrl,
                    'response_status' => $response->getStatusCode()
                ]);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Fallback email method failed', [
                'error' => $e->getMessage(),
                'student_email' => $studentEmail,
                'status' => $status
            ]);
            return false;
        }
    }
}