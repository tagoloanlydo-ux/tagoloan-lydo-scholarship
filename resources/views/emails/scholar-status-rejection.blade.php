<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Application Update</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 20px;
        }
        .message {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .reason-section {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .reason-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
            display: block;
        }
        .reason-text {
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 15px;
            color: #4b5563;
            line-height: 1.5;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        .contact-info {
            background-color: #f3f4f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 6px;
            text-align: center;
        }
        .contact-info p {
            margin: 5px 0;
            color: #374151;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 120px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">
                <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Scholarship Logo">
            </div>
            <h1>Scholarship Application Update</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Dear {{ $applicant_fname }} {{ $applicant_lname }},
            </div>

            <div class="message">
                <p>We regret to inform you that after careful review of your scholarship application, we are unable to approve your request at this time.</p>
            </div>

            <div class="reason-section">
                <span class="reason-label">Reason for Rejection:</span>
                <div class="reason-text">
                    {{ $reason }}
                </div>
            </div>

            <p>We appreciate your interest in the LYDO Scholarship Program and encourage you to consider reapplying in future application cycles when you meet the eligibility requirements.</p>

            <p>If you have any questions about this decision or need clarification regarding the rejection reason, please don't hesitate to contact our office.</p>

            <div class="contact-info">
                <p><strong>LYDO Scholarship Office</strong></p>
                <p>Tagoloan, Misamis Oriental</p>
                <p>Email: scholarship@lydo.gov.ph</p>
                <p>Phone: (088) 123-4567</p>
            </div>

            <p>Thank you for your understanding.</p>

            <p>Best regards,<br>
            <strong>LYDO Scholarship Committee</strong><br>
            Local Youth Development Office<br>
            Municipality of Tagoloan</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                This is an automated message from the LYDO Scholarship Management System.<br>
                Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
