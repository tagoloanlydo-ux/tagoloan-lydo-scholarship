<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document Approved Notification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background-color: #f9f9f9; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
        .document-info { background-color: #e8f5e9; padding: 15px; border-left: 4px solid #4CAF50; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Document Approved</h2>
            <p>LYDO Scholarship Program</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $applicant_fname }} {{ $applicant_lname }},</p>
            
            <div class="document-info">
                <p><strong>Good news!</strong> Your updated document has been reviewed and approved.</p>
                <p><strong>Document Type:</strong> {{ $document_type }}</p>
                @if(isset($document_name))
                <p><strong>Document:</strong> {{ $document_name }}</p>
                @endif
            </div>
            
            <p>Your document for the renewal application has been marked as <strong>GOOD</strong> and meets all the requirements.</p>
            
            <p>You may check the status of your renewal application through the scholar portal.</p>
            
            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>Continue to monitor your application status</li>
                <li>Ensure all other requirements are complete</li>
                <li>Wait for final renewal approval notification</li>
            </ul>
            
            <p>If you have any questions, please contact the LYDO Scholarship Office.</p>
            
            <p>Best regards,<br>
            <strong>LYDO Scholarship Committee</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} LYDO Scholarship Program. All rights reserved.</p>
        </div>
    </div>
</body>
</html>