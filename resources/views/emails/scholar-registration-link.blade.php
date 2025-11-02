<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your LYDO Scholar Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4361ee;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #4cc9f0;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LYDO Scholarship</h1>
        <h2>Update Your Account Credentials</h2>
    </div>
    <div class="content">
        <p>Dear Scholar,</p>
        <p>Congratulations! Your application has been approved. Create your username and password and access your scholar account, please click the link below:</p>
        <p style="text-align: center;">
           <a href="{{ $registration_link }}">Create Your Username and Password</a>
        </p>
        <p><strong>Important:</strong> This link will expire in 24 hours and can only be used once. If you encounter any issues, please contact the LYDO Scholarship office.</p>
        <p>Thank you for your interest in the LYDO Scholarship program.</p>
        <p>Best regards,<br>LYDO Scholarship Team</p>
    </div>
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
