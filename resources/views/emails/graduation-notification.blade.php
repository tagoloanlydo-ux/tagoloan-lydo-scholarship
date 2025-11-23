<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scholarship Graduation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #7c3aed, #4c1d95);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e2e8f0;
        }
        .congratulations {
            font-size: 24px;
            color: #7c3aed;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #7c3aed;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 14px;
        }
        .highlight {
            background-color: #f0f9ff;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border: 1px solid #bae6fd;
        }
        .certificate-section {
            background: linear-gradient(135deg, #fff9ed, #ffecc7);
            border: 2px solid #f59e0b;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
        }
        .certificate-icon {
            font-size: 48px;
            color: #f59e0b;
            margin-bottom: 15px;
        }
        .certificate-title {
            color: #d97706;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .certificate-description {
            color: #92400e;
            margin-bottom: 20px;
        }
        .attachment-info {
            background: #dcfce7;
            border: 1px solid #22c55e;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
        }
        .attachment-icon {
            font-size: 24px;
            color: #22c55e;
            margin-bottom: 10px;
        }
        .instructions {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            font-size: 14px;
        }
        .instructions ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 Scholarship Graduation</h1>
        <p>LYDO Scholarship Program</p>
    </div>
    
    <div class="content">
        <div class="congratulations">
            Congratulations, {{ $scholar_name }}!
        </div>
        
        <p>We are delighted to inform you that you have successfully completed the LYDO Scholarship Program. Your dedication and hard work have been recognized, and we are proud to celebrate this significant achievement with you.</p>
        
        <div class="details">
            <h3>Graduation Details:</h3>
            <p><strong>Scholar ID:</strong> {{ $scholar_id }}</p>
            <p><strong>School:</strong> {{ $school }}</p>
            <p><strong>Course:</strong> {{ $course }}</p>
            <p><strong>Graduation Date:</strong> {{ $graduation_date }}</p>
        </div>
        


        <div class="attachment-info">
            <div class="attachment-icon">📎</div>
            <p><strong>Certificate Attached:</strong> Graduation-Certificate-{{ $scholar_id }}.pdf</p>
            <p>Check your email attachments to download your official certificate.</p>
        </div>
        
        <div class="highlight">
            <h4>🎉 What This Means:</h4>
            <ul>
                <li>You have successfully completed the scholarship program requirements</li>
                <li>Your scholar status has been updated to "Graduated"</li>
                <li>The scholar account is no longer accessible</li>

                <li>Official certificate attached to this email for your records</li>
                <li>This marks the successful completion of your educational journey with LYDO support</li>
            </ul>
        </div>
        
        <div class="instructions">
        <p>With warmest congratulations,</p>
        <p><strong>The LYDO Scholarship Team</strong></p>
    </div>
    
    <div class="footer">
        <p>© {{ $current_year }} LYDO Scholarship Program. All rights reserved.</p>
        <p>This is an automated notification. Please do not reply to this email.</p>
    </div>
</body>
</html>