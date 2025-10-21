<!DOCTYPE html>
<html>
<head>
    <title>Scholarship Renewal Status Update</title>
</head>
<body>
    <h1>Scholarship Renewal Status</h1>
    <p>Dear {{ $applicant_fname }} {{ $applicant_lname }},</p>
    <p>We regret to inform you that your scholarship renewal application has been rejected.</p>
    @if(isset($reason) && $reason)
    <p><strong>Reason for Rejection:</strong> {{ $reason }}</p>
    @endif
    <p>As a result, your scholar account has been set to inactive and can no longer be reopened.</p>
    <p>If you have any questions, please contact us at scholarship@lydo.gov.</p>
    <p>Best regards,<br>LYDO Scholarship Team</p>
</body>
</html>
