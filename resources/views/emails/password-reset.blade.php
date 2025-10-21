<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
<p>Hello,</p>
<p>You requested a password reset for your LYDO Scholarship account.</p>
<p>Click the link below to reset your password:</p>
<a href="{{ url('reset-password/'.$token) }}">Reset Password</a>
<p>If you did not request this, please ignore.</p>

</body>
</html>
