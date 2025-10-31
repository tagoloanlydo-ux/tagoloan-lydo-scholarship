<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Application Approved</title>
</head>
<body>
    <h1>Congratulations, {{ $applicant_fname }} {{ $applicant_lname }}!</h1>
    <p>Your scholarship application has been approved.</p>
    <p>Please fill out the intake sheet by clicking the link below:</p>
    <p><a href="{{ url('/intake-sheet/' . $applicationPersonnelId) }}">Fill Intake Sheet</a></p>
    <p>Best regards,<br>LYDO Scholarship Team</p>
</body>
</html>
