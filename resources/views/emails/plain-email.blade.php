<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject }}</title>
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
            background: linear-gradient(135deg, #4c1d95 0%, #7e22ce 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8fafc;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e5e7eb;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lydo Scholarship</h1>
        <h2>{{ $subject }}</h2>
    </div>
    
    <div class="content">
        {!! nl2br(e($emailMessage)) !!}
    </div>
    
    <div class="footer">
        <p>This is an automated message from Lydo Scholarship Management System.</p>
        <p>Please do not reply to this email.</p>
    </div>
</body>
</html>