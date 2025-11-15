<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Disbursement Schedule</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            background-color: #f8f9fa;
            border-radius: 0 0 10px 10px;
        }
        .notification-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #667eea;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .important-note {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LYDO Scholarship Disbursement Schedule</h1>
        </div>
        
        <div class="content">
            <div class="notification-box">
                <h2>Hello {{ $scholar_name }},</h2>
                <p>We are pleased to inform you that your scholarship disbursement has been scheduled.</p>
                
                <div class="info-item">
                    <span class="info-label">Disbursement Date:</span> {{ $disbursement_date }}
                </div>
                
                <div class="info-item">
                    <span class="info-label">Academic Period:</span> {{ $semester }} - {{ $academic_year }}
                </div>
                
                <div class="info-item">
                    <span class="info-label">Amount:</span> ₱{{ $amount }}
                </div>
                
                <div class="info-item">
                    <span class="info-label">Scholar ID:</span> {{ $scholar_id }}
                </div>
            </div>
            
            <div class="important-note">
                <strong>Important Reminders:</strong>
                <ul>
                    <li>Please bring your valid school ID and any other required identification</li>
                    <li>Be on time for the scheduled disbursement</li>
                    <li>Ensure all your documents are complete and updated</li>
                    <li>Contact the LYDO office if you have any questions or concerns</li>
                </ul>
            </div>
            
            <p>We congratulate you on your continued academic journey and wish you success in your studies!</p>
            
            <p>Best regards,<br>
            <strong>LYDO Scholarship Program</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>If you have any questions, please contact the LYDO Scholarship Office.</p>
        </div>
    </div>
</body>
</html>