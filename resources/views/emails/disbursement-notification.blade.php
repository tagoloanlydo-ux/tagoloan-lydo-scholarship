<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Disbursement Schedule</title>
    <style>
        /* Your existing styles remain the same */
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
    <span class="info-label">Time:</span> {{ $disbursement_time ?? 'To be announced' }}
</div>

<div class="info-item">
    <span class="info-label">Location:</span> {{ $disbursement_location ?? 'To be announced' }}
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
                    <li>Be on time for the scheduled disbursement at the specified location</li>
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