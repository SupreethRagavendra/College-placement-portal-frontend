<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - {{ $collegeName }}</title>
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
    <div class="email-container">
        <div class="header">
            <h1>🎉 Account Approved!</h1>
            <p>Welcome to {{ $collegeName }}</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear <strong>{{ $studentName }}</strong>,
            </div>
            
            <div class="message-box">
                <p><strong>Your account has been approved for the College Placement Training Portal!</strong></p>
                <div class="status-badge">Status: Approved</div>
                <p>You can now access the portal to enhance your skills and prepare for placements.</p>
            </div>
            
            <div class="next-steps">
                <h3>🚀 What's Next?</h3>
                <ul>
                    <li><strong>Access the Portal:</strong> Log in to your training dashboard</li>
                    <li><strong>Complete Your Profile:</strong> Add your academic details and skills</li>
                    <li><strong>Take Assessments:</strong> Practice with skill assessments across various topics</li>
                    <li><strong>Use AI Chatbot:</strong> Get instant help and guidance from our intelligent assistant</li>
                    <li><strong>Track Progress:</strong> Monitor your performance and improvement over time</li>
                    <li><strong>Prepare for Placements:</strong> Build confidence through comprehensive training</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $portalUrl }}/login" class="cta-button">🚀 Access Portal Now</a>
            </div>
            
            <div class="contact-info">
                <h4>📞 Need Help?</h4>
                <p>If you have any questions or need assistance getting started, don't hesitate to reach out:</p>
                <p><strong>Support Email:</strong> {{ config('mail.from.address') }}<br>
                <strong>Support Hours:</strong> Monday - Friday, 9:00 AM - 6:00 PM</p>
            </div>
            
            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>{{ $collegeName }} Training Team</strong>
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