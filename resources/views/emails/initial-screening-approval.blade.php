<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial Screening Approval - LYDO Scholarship</title>
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
            background-color: #4CAF50;
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
        <h2>Initial Screening Approval</h2>
    </div>

    <div class="content">
        <p>Dear {{ $applicant_fname }} {{ $applicant_lname }},</p>

        <p>Congratulations! We are pleased to inform you that your application for the LYDO Scholarship has passed the initial screening.</p>

        <p>Please wait for an announcement regarding the date and time for your face-to-face interview. We will notify you via email once the schedule is finalized.</p>

        <p>If you have any questions, feel free to contact us.</p>

        <p>Best regards,<br>
        LYDO Scholarship Team</p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>For inquiries, contact us at support@lydoscholarship.com</p>
    </div>
</body>
</html>
