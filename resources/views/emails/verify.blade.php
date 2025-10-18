<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f6f9fc; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden;">
        <tr>
            <td style="background:linear-gradient(135deg,#667eea,#764ba2); color:#ffffff; padding:24px; text-align:center;">
                <h2 style="margin:0;">College Placement Portal</h2>
            </td>
        </tr>
        <tr>
            <td style="padding:24px; color:#333333;">
                <p>Hi {{ $user->name }},</p>
                <p>Thanks for registering. Please confirm your email address by clicking the button below. This link will expire in 1 hour.</p>
                <p style="text-align:center; margin:32px 0;">
                    <a href="{{ $url }}" style="background:#667eea; color:#ffffff; padding:12px 20px; text-decoration:none; border-radius:6px; display:inline-block;">Verify Email</a>
                </p>
                <p>If the button doesn't work, copy and paste this URL into your browser:</p>
                <p style="word-break:break-all; color:#555555;">{{ $url }}</p>
                <p style="margin-top:24px; color:#666666;">If you didn't create an account, you can safely ignore this email.</p>
                <p>Regards,<br>College Placement Portal Team</p>
            </td>
        </tr>
    </table>
</body>
</html>


