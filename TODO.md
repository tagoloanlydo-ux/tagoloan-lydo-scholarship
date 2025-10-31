# TODO: Implement Email Notification for Initial Screening Approval with Intake Sheet Link

## 1. Add Public Route for Intake Sheet
- [ ] Add route in routes/web.php for public access to intake sheet using application_personnel_id and token.

## 2. Create InitialScreeningApproval Mailable Class
- [ ] Create app/Mail/InitialScreeningApproval.php with email content and link.

## 3. Update MayorStaffController
- [ ] Add showPublicIntakeSheet method to handle public intake sheet access with token verification.
- [ ] Modify updateInitialScreening method to send email upon approval.

## 4. Update Email Template
- [ ] Update resources/views/emails/initial-screening-approval.blade.php to include the intake sheet link.
