<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Status Update - {{ $collegeName }}</title>
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
    <div class="email-container">
        <div class="header">
            <h1>Application Status Update</h1>
            <p>{{ $collegeName }}</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear <strong>{{ $studentName }}</strong>,
            </div>
            
            <div class="message-box">
                <p>Thank you for your interest in joining {{ $collegeName }} Training Portal. After careful review, we regret to inform you that your account registration has been <strong>not approved</strong> at this time.</p>
                <div class="status-badge">Status: Not Approved</div>
            </div>
            
            @if($rejectionReason)
            <div class="reason-box">
                <h4>📝 Reason for Decision:</h4>
                <p>{{ $rejectionReason }}</p>
            </div>
            @endif
            
            <div class="next-steps">
                <h3>💡 What You Can Do:</h3>
                <ul>
                    <li><strong>Review Feedback:</strong> Consider the reason provided above (if applicable)</li>
                    <li><strong>Address Issues:</strong> Work on any areas mentioned in the feedback</li>
                    <li><strong>Reapply:</strong> You may submit a new application after addressing concerns</li>
                    <li><strong>Seek Guidance:</strong> Contact our support team for clarification</li>
                    <li><strong>Prepare Better:</strong> Use this as an opportunity to strengthen your application</li>
                </ul>
            </div>
            
            <p>We encourage you to reapply once you've addressed the mentioned concerns. Our support team is always here to help guide you through the process.</p>
            
            <div class="contact-info">
                <h4>📞 Need Clarification?</h4>
                <p>For questions about your application or guidance on reapplying:</p>
                <p><strong>Support Email:</strong> {{ config('mail.from.address') }}<br>
                <strong>Support Hours:</strong> Monday - Friday, 9:00 AM - 6:00 PM</p>
            </div>
            
            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>{{ $collegeName }} Support Team</strong>
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply directly to this email.</p>
            <p>For support, contact us at {{ config('mail.from.address') }}</p>
            <p>© {{ date('Y') }} {{ $collegeName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>