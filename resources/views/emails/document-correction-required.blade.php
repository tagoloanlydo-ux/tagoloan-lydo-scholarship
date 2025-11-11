<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document Correction Required</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4c1d95; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .document-item { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #ef4444; }
        .button { display: inline-block; background: #4c1d95; color: #fff; padding: 10px 16px; text-decoration: none; border-radius: 4px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>LYDO Scholarship</h1>
            <h2>Document Correction Required</h2>
        </div>
        
        <div class="content">
            <p>Dear {{ $applicant_fname }} {{ $applicant_lname }},</p>
            
            <p>Your scholarship renewal application requires some document corrections. Please review the following:</p>
            
            <h3>Documents Requiring Correction:</h3>
            
            @foreach($bad_documents as $document)
            <div class="document-item">
                <strong>{{ $document['name'] }}</strong>
                <p><strong>Reason:</strong> {{ $document['reason'] }}</p>
            </div>
            @endforeach
            
            <p>Please update your renewal application in your Scholar account by clicking the <strong>Renewal</strong> button below and uploading the corrected documents.</p>

            <p>
                <a href="{{ $renewal_url ?? '#' }}" class="button">Renewal</a>
            </p>
            
            <p>Best regards,<br>LYDO Scholarship Team</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>