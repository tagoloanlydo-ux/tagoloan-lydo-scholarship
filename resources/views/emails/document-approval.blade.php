<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document Approved - LYDO Scholarship</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e0e0e0;
        }
        .highlight {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #777;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📄 Document Approved</h1>
        <p>LYDO Scholarship Program</p>
    </div>
    
    <div class="content">
        <p>Dear <strong>{{ $applicant_fname }} {{ $applicant_lname }}</strong>,</p>
        
        <p>We are pleased to inform you that your document has been reviewed and approved by the Mayor's Staff.</p>
        
        <div class="highlight">
            <h3>✅ Document Approved</h3>
            <p><strong>Document:</strong> {{ $document_name }}</p>
            <p><strong>Status:</strong> <span class="badge">APPROVED</span></p>
            <p><strong>Date Approved:</strong> {{ $date_approved }}</p>
        </div>
        
        <p>Your document has met all the necessary requirements and is now considered valid for your scholarship application.</p>
        
        <p><strong>Next Steps:</strong></p>
        <ul>
            <li>Continue with your scholarship application process</li>
            <li>Ensure all other required documents are submitted</li>
            <li>Check your email regularly for further updates</li>
        </ul>
        
        <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <strong>Mayor's Office - LYDO Scholarship Program</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>© {{ date('Y') }} LYDO Scholarship Program. All rights reserved.</p>
    </div>
</body>
</html>