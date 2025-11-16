<!DOCTYPE html>
<html>
<head>
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .content {
            margin-bottom: 30px;
        }
        .content p {
            margin: 0 0 15px 0;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .button {
            display: inline-block;
            background-color: #7c3aed;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #6d28d9;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('images/LYDO.png') }}" alt="LYDO Scholarship Team" />
            <h2 style="color: #7c3aed; margin-top: 10px;">{{ $subject }}</h2>
        </div>
        <div class="content">
            {!! nl2br(e($emailMessage)) !!}
        </div>
        <div class="footer">
            <p>Best regards,<br>LYDO Scholarship Team</p>
        </div>
    </div>
</body>
</html>
