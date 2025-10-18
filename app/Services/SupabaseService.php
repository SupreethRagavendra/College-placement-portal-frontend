<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SupabaseService
{
    protected $client;
    protected $url;
    protected $anonKey;
    protected $serviceRoleKey;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10, // Reduced timeout to 10 seconds for faster fallback
            'connect_timeout' => 5, // 5 seconds for connection
            'verify' => false, // For development only
        ]);
        
        $this->url = config('supabase.url');
        $this->anonKey = config('supabase.anon_key');
        $this->serviceRoleKey = config('supabase.service_role_key');
    }

    /**
     * Build common headers for PostgREST requests
     */
    protected function buildHeaders(bool $useServiceRole = false, array $extra = []): array
    {
        $base = [
            'apikey' => $useServiceRole ? $this->serviceRoleKey : $this->anonKey,
            'Authorization' => 'Bearer ' . ($useServiceRole ? $this->serviceRoleKey : $this->anonKey),
        ];

        return array_merge($base, $extra);
    }

    /**
     * Select rows from a table using PostgREST query params
     * $filters example: ['category' => 'eq.Aptitude', 'id' => 'eq.10']
     */
    public function selectFrom(string $table, array $filters = [], array $options = []): array
    {
        // Create a cache key based on the parameters
        $cacheKey = "supabase_select_{$table}_" . md5(serialize([$filters, $options]));
        
        // Try to get from cache first (cache for 2 minutes)
        return Cache::remember($cacheKey, 120, function() use ($table, $filters, $options) {
            $query = [];
            foreach ($filters as $key => $value) {
                $query[$key] = $value;
            }
            if (isset($options['select'])) {
                $query['select'] = $options['select'];
            }
            if (isset($options['order'])) {
                // order: ['column' => 'created_at', 'asc' => false]
                $order = $options['order'];
                $query['order'] = ($order['column'] ?? 'id') . '.' . ((isset($order['asc']) && $order['asc']) ? 'asc' : 'desc');
            }
            if (isset($options['limit'])) {
                $query['limit'] = (string)$options['limit'];
            }
            if (isset($options['offset'])) {
                $query['offset'] = (string)$options['offset'];
            }

            $headers = $this->buildHeaders(true, [
                'Content-Type' => 'application/json',
            ]);

            $response = $this->client->get($this->url . '/rest/v1/' . $table, [
                'headers' => $headers,
                'query' => $query,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        });
    }

    /**
     * Insert one row into a table
     */
    public function insertInto(string $table, array $data): array
    {
        $headers = $this->buildHeaders(true, [
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation',
        ]);

        $response = $this->client->post($this->url . '/rest/v1/' . $table, [
            'headers' => $headers,
            'json' => $data,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Update a row by id
     */
    public function updateById(string $table, $id, array $data): array
    {
        $headers = $this->buildHeaders(true, [
            'Content-Type' => 'application/json',
            'Prefer' => 'return=representation',
        ]);

        $response = $this->client->patch($this->url . '/rest/v1/' . $table, [
            'headers' => $headers,
            'query' => [ 'id' => 'eq.' . $id ],
            'json' => $data,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Delete a row by id
     */
    public function deleteById(string $table, $id): void
    {
        $headers = $this->buildHeaders(true, [
            'Content-Type' => 'application/json',
        ]);

        $this->client->delete($this->url . '/rest/v1/' . $table, [
            'headers' => $headers,
            'query' => [ 'id' => 'eq.' . $id ],
        ]);
    }

    /**
     * Register a new user with Supabase Auth
     */
    public function signUp($email, $password, $userData = [])
    {
        try {
            $redirectUrl = config('supabase.redirect_url');
            
            Log::info('Supabase SignUp Request', [
                'email' => $email,
                'redirect_url' => $redirectUrl,
                'user_data' => $userData
            ]);

            $response = $this->client->post($this->url . '/auth/v1/signup', [
                'headers' => [
                    'apikey' => $this->anonKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password,
                    'data' => $userData,
                    'email_redirect_to' => $redirectUrl
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Supabase SignUp Response', [
                'status_code' => $response->getStatusCode(),
                'response_data' => $responseData
            ]);

            return $responseData;
        } catch (RequestException $e) {
            Log::error('Supabase SignUp Error: ' . $e->getMessage(), [
                'response_body' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body',
                'status_code' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'No status code'
            ]);
            throw new \Exception('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Login user with Supabase Auth
     */
    public function signIn($email, $password)
    {
        try {
            $response = $this->client->post($this->url . '/auth/v1/token?grant_type=password', [
                'headers' => [
                    'apikey' => $this->anonKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Supabase SignIn Error: ' . $e->getMessage());
            throw new \Exception('Login failed: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset email
     */
    public function resetPasswordForEmail($email)
    {
        try {
            $response = $this->client->post($this->url . '/auth/v1/recover', [
                'headers' => [
                    'apikey' => $this->anonKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => $email
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Supabase Password Reset Error: ' . $e->getMessage());
            throw new \Exception('Password reset failed: ' . $e->getMessage());
        }
    }

    /**
     * Get user by access token
     */
    public function getUser($accessToken)
    {
        try {
            $response = $this->client->get($this->url . '/auth/v1/user', [
                'headers' => [
                    'apikey' => $this->anonKey,
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ]
            ]);

            $userData = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Supabase GetUser Response', [
                'status_code' => $response->getStatusCode(),
                'user_data' => $userData
            ]);

            return $userData;
        } catch (RequestException $e) {
            Log::error('Supabase GetUser Error: ' . $e->getMessage(), [
                'response_body' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body',
                'status_code' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'No status code'
            ]);
            throw new \Exception('Failed to get user: ' . $e->getMessage());
        }
    }

    /**
     * Update user metadata
     */
    public function updateUser($accessToken, $userData)
    {
        try {
            $response = $this->client->put($this->url . '/auth/v1/user', [
                'headers' => [
                    'apikey' => $this->anonKey,
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $userData
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Supabase UpdateUser Error: ' . $e->getMessage());
            throw new \Exception('Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user (admin only)
     */
    public function deleteUser($userId)
    {
        try {
            $response = $this->client->delete($this->url . '/auth/v1/admin/users/' . $userId, [
                'headers' => [
                    'apikey' => $this->serviceRoleKey,
                    'Authorization' => 'Bearer ' . $this->serviceRoleKey,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Supabase DeleteUser Error: ' . $e->getMessage());
            throw new \Exception('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Get all users (admin only)
     */
    public function getAllUsers()
    {
        try {
            $response = $this->client->get($this->url . '/auth/v1/admin/users', [
                'headers' => [
                    'apikey' => $this->serviceRoleKey,
                    'Authorization' => 'Bearer ' . $this->serviceRoleKey,
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Supabase GetAllUsers Error: ' . $e->getMessage());
            throw new \Exception('Failed to get users: ' . $e->getMessage());
        }
    }

    /**
     * Update user password (admin only)
     */
    public function updateUserPassword($userId, $newPassword)
    {
        try {
            $response = $this->client->put($this->url . '/auth/v1/admin/users/' . $userId, [
                'headers' => [
                    'apikey' => $this->serviceRoleKey,
                    'Authorization' => 'Bearer ' . $this->serviceRoleKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'password' => $newPassword
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error('Supabase UpdateUserPassword Error: ' . $e->getMessage());
            throw new \Exception('Failed to update password: ' . $e->getMessage());
        }
    }

    /**
     * Call a Supabase Edge Function
     */
    public function callFunction(string $functionName, array $payload = [], bool $async = false)
    {
        try {
            $headers = [
                'apikey' => $this->anonKey,
                'Authorization' => 'Bearer ' . $this->anonKey,
                'Content-Type' => 'application/json',
            ];

            $options = [
                'headers' => $headers,
                'json' => $payload,
                'timeout' => $async ? 5 : 30, // Shorter timeout for async calls
            ];

            $url = $this->url . '/functions/v1/' . $functionName;

            if ($async) {
                // Return a promise for async calls
                return $this->client->postAsync($url, $options);
            } else {
                // Synchronous call
                $response = $this->client->post($url, $options);
                return json_decode($response->getBody()->getContents(), true);
            }
        } catch (RequestException $e) {
            Log::error('Supabase Edge Function Error: ' . $e->getMessage(), [
                'function_name' => $functionName,
                'payload' => $payload,
                'response_body' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body',
                'status_code' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : 'No status code'
            ]);
            
            if ($async) {
                // For async calls, log error but don't throw exception
                return null;
            }
            
            throw new \Exception('Edge function call failed: ' . $e->getMessage());
        }
    }

    /**
     * Send email notification asynchronously using multiple methods
     */
    public function sendStatusEmailAsync(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): ?PromiseInterface
    {
        // IMPORTANT: Prioritize Laravel Mail to send emails to actual students
        // Formspree is designed to send submissions to a fixed email (supreethvennila@gmail.com)
        // So we skip Formspree for individual student notifications
        
        try {
            // Primary: Laravel Mail (sends to actual student email)
            $laravelMailResult = $this->sendViaLaravelMail($studentEmail, $studentName, $status, $rejectionReason);
            
            if ($laravelMailResult) {
                Log::info('Email sent successfully via Laravel Mail to student', [
                    'student_email' => $studentEmail,
                    'student_name' => $studentName,
                    'status' => $status,
                    'provider' => 'laravel_mail'
                ]);
                return null; // Laravel Mail is synchronous, so return null for promise
            }
        } catch (\Exception $e) {
            Log::warning('Laravel Mail failed, trying Supabase Edge Function', [
                'error' => $e->getMessage(),
                'student_email' => $studentEmail,
                'student_name' => $studentName
            ]);
        }

        // Secondary: Supabase Edge Function (sends to actual student email)
        $payload = [
            'student_email' => $studentEmail,
            'student_name' => $studentName,
            'status' => $status,
            'college_name' => config('app.name', 'College Placement Portal')
        ];

        if ($rejectionReason && $status === 'rejected') {
            $payload['rejection_reason'] = $rejectionReason;
        }

        Log::info('Sending status email via Supabase Edge Function to student', [
            'student_email' => $studentEmail,
            'student_name' => $studentName,
            'status' => $status,
            'has_rejection_reason' => !empty($rejectionReason)
        ]);

        return $this->callFunction('send-status-email', $payload, true);
    }

    /**
     * Send email notification synchronously (for testing)
     */
    public function sendStatusEmail(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): array
    {
        // IMPORTANT: Prioritize Laravel Mail to send emails to actual students
        // Formspree is designed to send submissions to a fixed email (supreethvennila@gmail.com)
        // So we skip Formspree for individual student notifications
        
        try {
            // Primary: Laravel Mail (sends to actual student email)
            $laravelMailResult = $this->sendViaLaravelMail($studentEmail, $studentName, $status, $rejectionReason);
            
            if ($laravelMailResult) {
                Log::info('Email sent successfully via Laravel Mail to student (sync)', [
                    'student_email' => $studentEmail,
                    'student_name' => $studentName,
                    'status' => $status,
                    'provider' => 'laravel_mail'
                ]);
                return $laravelMailResult;
            }
        } catch (\Exception $e) {
            Log::warning('Laravel Mail failed, trying Supabase Edge Function', [
                'error' => $e->getMessage(),
                'student_email' => $studentEmail,
                'student_name' => $studentName
            ]);
        }

        // Secondary: Supabase Edge Function (sends to actual student email)
        $payload = [
            'student_email' => $studentEmail,
            'student_name' => $studentName,
            'status' => $status,
            'college_name' => config('app.name', 'College Placement Portal')
        ];

        if ($rejectionReason && $status === 'rejected') {
            $payload['rejection_reason'] = $rejectionReason;
        }

        Log::info('Sending status email via Supabase Edge Function to student (sync)', [
            'student_email' => $studentEmail,
            'student_name' => $studentName,
            'status' => $status,
            'has_rejection_reason' => !empty($rejectionReason)
        ]);

        return $this->callFunction('send-status-email', $payload, false);
    }

    /**
     * Send email via Laravel Mail (most reliable method)
     */
    private function sendViaLaravelMail(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): array
    {
        try {
            $collegeName = config('app.name', 'College Placement Portal');
            
            // Create the mailable
            $mailable = new \App\Mail\StudentStatusMail($studentName, $status, $rejectionReason, $collegeName);
            
            // Send the email
            \Illuminate\Support\Facades\Mail::to($studentEmail)->send($mailable);
            
            return [
                'success' => true,
                'provider' => 'laravel_mail',
                'recipient' => $studentEmail,
                'status' => $status,
                'template' => 'blade_template',
                'method' => 'smtp'
            ];
            
        } catch (\Exception $e) {
            Log::error('Laravel Mail sending failed', [
                'error' => $e->getMessage(),
                'student_email' => $studentEmail,
                'status' => $status
            ]);
            throw $e;
        }
    }

    /**
     * Send email via Formspree (user preferred method)
     */
    private function sendViaFormspree(string $studentEmail, string $studentName, string $status, ?string $rejectionReason = null): array
    {
        $collegeName = config('app.name', 'College Placement Portal');
        $formspreeEndpoint = 'https://formspree.io/f/xanpndqw'; // User preferred endpoint
        
        // Generate email content
        $emailContent = $this->generateEmailContent($studentName, $status, $rejectionReason, $collegeName);
        
        $postData = [
            'email' => $studentEmail,
            'name' => $studentName,
            'subject' => $emailContent['subject'],
            'message' => $emailContent['textContent'],
            'html' => $emailContent['htmlContent'], // Add HTML content for rich formatting
            '_replyto' => $studentEmail,
            '_subject' => $emailContent['subject'],
            'status' => $status,
            'college_name' => $collegeName,
            '_format' => 'html' // Tell Formspree to use HTML format
        ];
        
        if ($rejectionReason && $status === 'rejected') {
            $postData['rejection_reason'] = $rejectionReason;
        }

        $response = $this->client->post($formspreeEndpoint, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'json' => $postData,
            'timeout' => 30
        ]);

        if ($response->getStatusCode() === 200) {
            return [
                'success' => true,
                'provider' => 'formspree',
                'recipient' => $studentEmail,
                'status' => $status,
                'template' => 'enhanced_professional',
                'response' => json_decode($response->getBody()->getContents(), true)
            ];
        } else {
            throw new \Exception('Formspree request failed with status: ' . $response->getStatusCode());
        }
    }

    /**
     * Generate email content for different statuses
     */
    private function generateEmailContent(string $studentName, string $status, ?string $rejectionReason, string $collegeName): array
    {
        if ($status === 'approved') {
            $htmlContent = $this->generateApprovedEmailHTML($studentName, $collegeName);
            $textContent = $this->generateApprovedEmailText($studentName, $collegeName);
            
            return [
                'subject' => "🎉 Account Approved - Welcome to {$collegeName}!",
                'textContent' => $textContent,
                'htmlContent' => $htmlContent
            ];
        } else {
            $htmlContent = $this->generateRejectedEmailHTML($studentName, $collegeName, $rejectionReason);
            $textContent = $this->generateRejectedEmailText($studentName, $collegeName, $rejectionReason);
            
            return [
                'subject' => "Application Status Update - {$collegeName}",
                'textContent' => $textContent,
                'htmlContent' => $htmlContent
            ];
        }
    }

    /**
     * Generate HTML template for approved students
     */
    private function generateApprovedEmailHTML(string $studentName, string $collegeName): string
    {
        $portalUrl = config('app.url', 'http://localhost:8000');
        $currentYear = date('Y');
        
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Account Approved - {$collegeName}</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 20px; 
                    background-color: #f4f4f4;
                }
                .email-container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 12px; 
                    overflow: hidden;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                    color: white; 
                    padding: 40px 30px; 
                    text-align: center;
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 600;
                }
                .header p { 
                    margin: 10px 0 0 0; 
                    font-size: 16px; 
                    opacity: 0.9;
                }
                .content { 
                    padding: 40px 30px;
                }
                .greeting { 
                    font-size: 18px; 
                    color: #2c3e50; 
                    margin-bottom: 20px;
                }
                .status-badge { 
                    display: inline-block;
                    background: #22c55e; 
                    color: white; 
                    padding: 8px 16px; 
                    border-radius: 20px; 
                    font-weight: 600; 
                    font-size: 14px;
                    margin: 15px 0;
                }
                .message-box { 
                    background: #f8fafc; 
                    border-left: 4px solid #22c55e; 
                    padding: 20px; 
                    margin: 25px 0; 
                    border-radius: 4px;
                }
                .next-steps { 
                    background: #ecfdf5; 
                    border: 1px solid #d1fae5; 
                    padding: 25px; 
                    margin: 25px 0; 
                    border-radius: 8px;
                }
                .next-steps h3 { 
                    color: #059669; 
                    margin-top: 0; 
                    font-size: 18px;
                }
                .next-steps ul { 
                    margin: 15px 0; 
                    padding-left: 20px;
                }
                .next-steps li { 
                    margin: 8px 0; 
                    color: #374151;
                }
                .cta-button { 
                    display: inline-block; 
                    background: #667eea; 
                    color: white !important; 
                    text-decoration: none; 
                    padding: 15px 30px; 
                    border-radius: 8px; 
                    font-weight: 600; 
                    margin: 25px 0;
                    transition: background-color 0.3s ease;
                }
                .cta-button:hover { 
                    background: #5a67d8; 
                }
                .footer { 
                    background: #f8fafc; 
                    padding: 30px; 
                    text-align: center; 
                    border-top: 1px solid #e5e7eb;
                }
                .footer p { 
                    margin: 5px 0; 
                    font-size: 14px; 
                    color: #6b7280;
                }
                .contact-info { 
                    background: #fff7ed; 
                    border: 1px solid #fed7aa; 
                    padding: 20px; 
                    margin: 20px 0; 
                    border-radius: 8px;
                }
                .contact-info h4 { 
                    color: #ea580c; 
                    margin-top: 0;
                }
                @media (max-width: 600px) {
                    .email-container { margin: 10px; }
                    .header, .content, .footer { padding: 20px; }
                    .header h1 { font-size: 24px; }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>🎉 Account Approved!</h1>
                    <p>Welcome to {$collegeName}</p>
                </div>
                
                <div class='content'>
                    <div class='greeting'>
                        Dear <strong>{$studentName}</strong>,
                    </div>
                    
                    <div class='message-box'>
                        <p><strong>Your account has been approved for the College Placement Portal!</strong></p>
                        <div class='status-badge'>Status: Approved</div>
                        <p>You can now access the portal and explore placement opportunities.</p>
                    </div>
                    
                    <div class='next-steps'>
                        <h3>🚀 What's Next?</h3>
                        <ul>
                            <li><strong>Access the Portal:</strong> Log in to explore available opportunities</li>
                            <li><strong>Complete Your Profile:</strong> Add your skills, experience, and preferences</li>
                            <li><strong>Browse Companies:</strong> Discover placement opportunities with top companies</li>
                            <li><strong>Take Assessments:</strong> Practice with skill assessments to improve your profile</li>
                            <li><strong>Apply for Positions:</strong> Start applying to relevant job openings</li>
                        </ul>
                    </div>
                    
                    <div style='text-align: center;'>
                        <a href='{$portalUrl}/login' class='cta-button'>🚀 Access Portal Now</a>
                    </div>
                    
                    <div class='contact-info'>
                        <h4>📞 Need Help?</h4>
                        <p>If you have any questions or need assistance getting started, don't hesitate to reach out:</p>
                        <p><strong>Email:</strong> supreethvennila@gmail.com<br>
                        <strong>Support Hours:</strong> Monday - Friday, 9:00 AM - 6:00 PM</p>
                    </div>
                    
                    <p style='margin-top: 30px;'>
                        Best regards,<br>
                        <strong>{$collegeName} Team</strong>
                    </p>
                </div>
                
                <div class='footer'>
                    <p>This is an automated message. Please do not reply directly to this email.</p>
                    <p>For support, contact us at supreethvennila@gmail.com</p>
                    <p>© {$currentYear} {$collegeName}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Generate text version for approved students
     */
    private function generateApprovedEmailText(string $studentName, string $collegeName): string
    {
        $portalUrl = config('app.url', 'http://localhost:8000');
        
        return "Dear {$studentName},\n\n" .
               "🎉 ACCOUNT APPROVED! 🎉\n\n" .
               "Your account has been approved for the College Placement Portal!\n\n" .
               "Status: Approved\n\n" .
               "You can now access the portal and explore placement opportunities.\n\n" .
               "WHAT'S NEXT?\n" .
               "━━━━━━━━━━━━\n" .
               "🚀 Access the Portal: Log in to explore available opportunities\n" .
               "👤 Complete Your Profile: Add your skills, experience, and preferences\n" .
               "🏢 Browse Companies: Discover placement opportunities with top companies\n" .
               "📝 Take Assessments: Practice with skill assessments to improve your profile\n" .
               "💼 Apply for Positions: Start applying to relevant job openings\n\n" .
               "ACCESS PORTAL: {$portalUrl}/login\n\n" .
               "NEED HELP?\n" .
               "━━━━━━━━━━\n" .
               "If you have any questions or need assistance getting started:\n" .
               "📧 Email: supreethvennila@gmail.com\n" .
               "🕒 Support Hours: Monday - Friday, 9:00 AM - 6:00 PM\n\n" .
               "Best regards,\n" .
               "{$collegeName} Team\n\n" .
               "---\n" .
               "This is an automated message. For support, contact supreethvennila@gmail.com";
    }

    /**
     * Generate HTML template for rejected students
     */
    private function generateRejectedEmailHTML(string $studentName, string $collegeName, ?string $rejectionReason): string
    {
        $currentYear = date('Y');
        $reasonSection = $rejectionReason ? 
            "<div class='reason-box'>
                <h4>📝 Reason for Decision:</h4>
                <p>{$rejectionReason}</p>
            </div>" : '';
        
        return "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Application Status Update - {$collegeName}</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 20px; 
                    background-color: #f4f4f4;
                }
                .email-container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: white; 
                    border-radius: 12px; 
                    overflow: hidden;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header { 
                    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); 
                    color: white; 
                    padding: 40px 30px; 
                    text-align: center;
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 28px; 
                    font-weight: 600;
                }
                .header p { 
                    margin: 10px 0 0 0; 
                    font-size: 16px; 
                    opacity: 0.9;
                }
                .content { 
                    padding: 40px 30px;
                }
                .greeting { 
                    font-size: 18px; 
                    color: #2c3e50; 
                    margin-bottom: 20px;
                }
                .status-badge { 
                    display: inline-block;
                    background: #ef4444; 
                    color: white; 
                    padding: 8px 16px; 
                    border-radius: 20px; 
                    font-weight: 600; 
                    font-size: 14px;
                    margin: 15px 0;
                }
                .message-box { 
                    background: #fef2f2; 
                    border-left: 4px solid #ef4444; 
                    padding: 20px; 
                    margin: 25px 0; 
                    border-radius: 4px;
                }
                .reason-box { 
                    background: #fee2e2; 
                    border: 1px solid #fecaca; 
                    padding: 20px; 
                    margin: 25px 0; 
                    border-radius: 8px;
                }
                .reason-box h4 { 
                    color: #dc2626; 
                    margin-top: 0;
                }
                .next-steps { 
                    background: #f0f9ff; 
                    border: 1px solid #bae6fd; 
                    padding: 25px; 
                    margin: 25px 0; 
                    border-radius: 8px;
                }
                .next-steps h3 { 
                    color: #0369a1; 
                    margin-top: 0; 
                    font-size: 18px;
                }
                .next-steps ul { 
                    margin: 15px 0; 
                    padding-left: 20px;
                }
                .next-steps li { 
                    margin: 8px 0; 
                    color: #374151;
                }
                .contact-info { 
                    background: #fff7ed; 
                    border: 1px solid #fed7aa; 
                    padding: 20px; 
                    margin: 20px 0; 
                    border-radius: 8px;
                }
                .contact-info h4 { 
                    color: #ea580c; 
                    margin-top: 0;
                }
                .footer { 
                    background: #f8fafc; 
                    padding: 30px; 
                    text-align: center; 
                    border-top: 1px solid #e5e7eb;
                }
                .footer p { 
                    margin: 5px 0; 
                    font-size: 14px; 
                    color: #6b7280;
                }
                @media (max-width: 600px) {
                    .email-container { margin: 10px; }
                    .header, .content, .footer { padding: 20px; }
                    .header h1 { font-size: 24px; }
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>Application Status Update</h1>
                    <p>{$collegeName}</p>
                </div>
                
                <div class='content'>
                    <div class='greeting'>
                        Dear <strong>{$studentName}</strong>,
                    </div>
                    
                    <div class='message-box'>
                        <p>Thank you for your interest in joining {$collegeName}. After careful review, we regret to inform you that your account registration has been <strong>not approved</strong> at this time.</p>
                        <div class='status-badge'>Status: Not Approved</div>
                    </div>
                    
                    {$reasonSection}
                    
                    <div class='next-steps'>
                        <h3>💡 What You Can Do:</h3>
                        <ul>
                            <li><strong>Review Feedback:</strong> Consider the reason provided above (if applicable)</li>
                            <li><strong>Address Issues:</strong> Work on any areas mentioned in the feedback</li>
                            <li><strong>Reapply:</strong> You may submit a new application after addressing concerns</li>
                            <li><strong>Seek Guidance:</strong> Contact our admissions team for clarification</li>
                            <li><strong>Prepare Better:</strong> Use this as an opportunity to strengthen your application</li>
                        </ul>
                    </div>
                    
                    <p>We encourage you to reapply once you've addressed the mentioned concerns. Our admissions team is always here to help guide you through the process.</p>
                    
                    <div class='contact-info'>
                        <h4>📞 Need Clarification?</h4>
                        <p>For questions about your application or guidance on reapplying:</p>
                        <p><strong>Email:</strong> supreethvennila@gmail.com<br>
                        <strong>Support Hours:</strong> Monday - Friday, 9:00 AM - 6:00 PM</p>
                    </div>
                    
                    <p style='margin-top: 30px;'>
                        Best regards,<br>
                        <strong>{$collegeName} Admissions Team</strong>
                    </p>
                </div>
                
                <div class='footer'>
                    <p>This is an automated message. Please do not reply directly to this email.</p>
                    <p>For support, contact us at supreethvennila@gmail.com</p>
                    <p>© {$currentYear} {$collegeName}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Generate text version for rejected students
     */
    private function generateRejectedEmailText(string $studentName, string $collegeName, ?string $rejectionReason): string
    {
        $reasonText = $rejectionReason ? "\n\nREASON FOR DECISION:\n━━━━━━━━━━━━━━━━━━━━\n{$rejectionReason}\n" : '';
        
        return "Dear {$studentName},\n\n" .
               "APPLICATION STATUS UPDATE\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
               "Thank you for your interest in joining {$collegeName}. After careful review, we regret to inform you that your account registration has been NOT APPROVED at this time.\n" .
               "Status: Not Approved" . $reasonText . "\n\n" .
               "WHAT YOU CAN DO:\n" .
               "━━━━━━━━━━━━━━━━━\n" .
               "🔍 Review Feedback: Consider the reason provided above (if applicable)\n" .
               "🔧 Address Issues: Work on any areas mentioned in the feedback\n" .
               "🔄 Reapply: You may submit a new application after addressing concerns\n" .
               "📞 Seek Guidance: Contact our admissions team for clarification\n" .
               "💪 Prepare Better: Use this as an opportunity to strengthen your application\n\n" .
               "We encourage you to reapply once you've addressed the mentioned concerns. Our admissions team is always here to help guide you through the process.\n\n" .
               "NEED CLARIFICATION?\n" .
               "━━━━━━━━━━━━━━━━━━━\n" .
               "For questions about your application or guidance on reapplying:\n" .
               "📧 Email: supreethvennila@gmail.com\n" .
               "🕒 Support Hours: Monday - Friday, 9:00 AM - 6:00 PM\n\n" .
               "Best regards,\n" .
               "{$collegeName} Admissions Team\n\n" .
               "---\n" .
               "This is an automated message. For support, contact supreethvennila@gmail.com";
    }
}
