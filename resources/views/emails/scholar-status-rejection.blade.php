<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application Rejection - LYDO Scholarship</title>
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
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .reason-box {
            background-color: #fff;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 3px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LYDO Scholarship</h1>
        <h2>Application Status Update</h2>
    </div>

    <div class="content">
        <p>Dear {{ $applicant_fname }} {{ $applicant_lname }},</p>

        <p>After careful review of your scholarship application, we regret to inform you that you are not applicable to become a LYDO scholar at this time.</p>

        <div class="reason-box">
            <strong>Reason:</strong><br>
            {{ $reason }}
        </div>

        <p>We appreciate your interest in the LYDO Scholarship program and encourage you to consider other educational opportunities that may be available to you.</p>

        <p>If you believe this decision was made in error or if you have additional information that may support your application, please contact us for further assistance.</p>

        <p>Thank you for your understanding.</p>

        <p>Best regards,<br>
        LYDO Scholarship Committee</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>For inquiries, contact us at support@lydoscholarship.com</p>
    </div>
</body>
</html>
