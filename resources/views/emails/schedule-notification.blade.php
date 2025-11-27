<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Schedule Notification - Lydo Scholarship</title>
    <style>
        /* your existing styles */
    </style>
</head>
<body>
    <div class="header">
        <h1>Lydo Scholarship</h1>
        <h2>Schedule Notification</h2>
    </div>
    
    <div class="content">
        {{-- FIX: Check if applicantName exists --}}
        @if(isset($applicantName) && !empty($applicantName))
            <p>Dear {{ $applicantName }},</p>
        @else
            <p>Dear Applicant,</p>
        @endif
        
        <div class="important">
            <strong>Important:</strong> You have a scheduled activity for your scholarship application.
        </div>

        {{-- rest of your template remains the same --}}
        <div class="schedule-details">
            <h3>Event Details:</h3>
            <div class="schedule-item">
                <strong>What:</strong> {{ $scheduleData['what'] ?? 'Not specified' }}
            </div>
            <div class="schedule-item">
                <strong>Where:</strong> {{ $scheduleData['where'] ?? 'Not specified' }}
            </div>
            <div class="schedule-item">
                <strong>Date:</strong> {{ $scheduleData['date'] ?? 'Not specified' }}
            </div>
            <div class="schedule-item">
                <strong>Time:</strong> {{ $scheduleData['time'] ?? 'Not specified' }}
            </div>
        </div>

        <p>Please make sure to:</p>
        <ul>
            <li>Arrive 15 minutes before the scheduled time</li>
            <li>Bring all required documents</li>
            <li>Bring valid identification</li>
            <li>Dress appropriately</li>
        </ul>

        <p>If you have any questions or cannot attend, please contact the LYDO office immediately.</p>

        <p>Best regards,<br>
        Lydo Scholarship Management Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from Lydo Scholarship Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>