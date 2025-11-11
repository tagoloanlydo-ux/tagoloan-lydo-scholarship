<!DOCTYPE html>
<html>
<head>
    <title>Document Correction Required</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .footer { padding: 20px; text-align: center; color: #6b7280; }
        .document-list { background: white; padding: 15px; border-radius: 5px; border-left: 4px solid #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Document Correction Required</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $applicant_fname }} {{ $applicant_lname }},</p>
            
            <p>Your scholarship renewal application requires document corrections. Please review the following documents that need to be updated:</p>
            
            <div class="document-list">
                <strong>Documents requiring correction:</strong>
                <pre style="white-space: pre-wrap; margin: 10px 0;">{{ $bad_documents_list }}</pre>
            </div>
            
            <p><strong>Deadline for correction:</strong> {{ $correction_deadline }}</p>
            
            <p>Please log in to your scholar portal to upload the corrected documents before the deadline.</p>
            
            <p>If you have any questions, please contact the scholarship office.</p>
        </div>
        
        <div class="footer">
            <p>Best regards,<br><strong>Lydo Scholarship Team</strong></p>
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>