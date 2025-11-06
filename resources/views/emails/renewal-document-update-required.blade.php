<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renewal Document Update Required - LYDO Scholarship</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #6B46C1;
            color: white;
            padding: 15px;
            text-align: left;
            border-radius: 5px 5px 0 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 0;
            font-size: 14px;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #000000ff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .document-info {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .document-info ul {
            margin: 0;
            padding-left: 20px;
        }
        .document-info li {
            margin-bottom: 5px;
        }
        .comment-box {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 15px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <div style="text-align: left;">
            <h1 style="margin-bottom: 0;">LYDO Scholarship</h1>
            <p style="margin-top: 5px; font-size: 14px; font-style: italic;">PARA SA KABATAAN, PARA SA KINABUKASAN.</p>
        </div>
        <h2>Renewal Document Update Required</h2>
    </div>
    <div class="content">
        <p>Dear {{ $applicant_fname }} {{ $applicant_lname }},</p>

        <p>Your scholarship renewal application has been reviewed and the following document needs to be updated:</p>

        <div class="document-info">
            <strong>Document Requiring Update:</strong>
            <ul>
                <li>{{ $document_type }}</li>
            </ul>
        </div>

        <div class="comment-box">
            <strong>Reviewer's Comment:</strong>
            <p>{{ $comment }}</p>
        </div>

        <p>Please log in to your scholarship portal and upload a new version of this document. The document that needs updating is: <strong>{{ $document_type }}</strong>.</p>

        <p><strong>Action Required:</strong> Upload the updated document within 7 days to continue with your renewal process. Once updated, the document will be re-reviewed by our staff.</p>

        <p>If you have any questions, please contact the scholarship office.</p>

        <p>Best regards,<br>Scholarship Management Team</p>
    </div>
    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
    </div>
</body>
</html>
