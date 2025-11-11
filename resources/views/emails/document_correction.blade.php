<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
        .step { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #667eea; border-radius: 5px; }
        .urgent { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .footer { text-align: center; margin-top: 20px; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Document Correction Required</h1>
            <p>Lydo Scholarship Renewal</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $applicant_fname }} {{ $applicant_lname }}</strong>,</p>
            
            <p>We've reviewed your scholarship renewal application and found that some documents require correction.</p>
            
            <div class="urgent">
                <h3>⚠️ Documents Requiring Correction:</h3>
                <pre style="font-family: Arial, sans-serif; white-space: pre-line;">{!! $bad_documents_list !!}</pre>
            </div>

            <h3>📝 How to Correct Your Documents:</h3>
            
            <div class="step">
                <h4>Step 1: Log into Your Scholar Account</h4>
                <p>Visit the Lydo Scholarship portal and log in using your credentials.</p>
            </div>

            <div class="step">
                <h4>Step 2: Access Renewal Section</h4>
                <p>Navigate to the "Renewal" section in your dashboard.</p>
            </div>

            <div class="step">
                <h4>Step 3: Update Bad Documents</h4>
                <p>For each document marked as "bad":</p>
                <ul>
                    <li>Click on the document upload button</li>
                    <li>Select the corrected version from your device</li>
                    <li>Ensure the document is clear and complete</li>
                    <li>Save the changes</li>
                </ul>
            </div>

            <div class="step">
                <h4>Step 4: Resubmit for Review</h4>
                <p>After updating all required documents, your application will be automatically re-submitted for review.</p>
            </div>

            <div class="urgent">
                <h4>⏰ Important Deadline</h4>
                <p>Please complete these corrections by: <strong>{{ $correction_deadline }}</strong></p>
                <p><em>Failure to correct documents by this date may result in delays in your scholarship renewal processing.</em></p>
            </div>

            <p>If you need assistance or have questions about the required corrections, please contact our support team.</p>

            <p>Best regards,<br>
            <strong>Lydo Scholarship Committee</strong></p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>© {{ date('Y') }} Lydo Scholarship Program. All rights reserved.</p>
        </div>
    </div>
</body>
</html>