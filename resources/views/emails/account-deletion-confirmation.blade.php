<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Deletion Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #dc3545;
            margin: 0;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .footer {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚠️ Account Deletion Request</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $user->name }},</p>
            
            <p>We received a request to <strong>permanently delete</strong> your account from the College Placement Portal.</p>
            
            <div class="warning">
                <strong>⚠️ WARNING:</strong> This action is irreversible! Once confirmed, the following will be permanently deleted:
                <ul>
                    <li>Your profile information</li>
                    <li>All assessment results and history</li>
                    <li>Performance analytics and progress data</li>
                    <li>Chat history and conversations</li>
                    <li>All other associated data</li>
                </ul>
            </div>
            
            <p><strong>If you initiated this request:</strong></p>
            <p>Please confirm your decision by clicking the button below. This link will expire in 24 hours.</p>
            
            <div class="buttons">
                <a href="{{ $confirmUrl }}" class="btn btn-danger">YES, DELETE MY ACCOUNT</a>
            </div>
            
            <p><strong>If you did NOT request this:</strong></p>
            <p>Your account is safe! Simply ignore this email or click the button below to cancel the deletion request.</p>
            
            <div class="buttons">
                <a href="{{ $cancelUrl }}" class="btn btn-success">CANCEL DELETION REQUEST</a>
            </div>
            
            <p>For security reasons, this deletion request will automatically expire in 24 hours if no action is taken.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated email from College Placement Portal. Please do not reply to this email.</p>
            <p>If you have any questions, please contact support.</p>
            <p>&copy; {{ date('Y') }} College Placement Portal. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
