# Task: Implement Real-Time Notification Bell with Sound for Approved Initial Screening and Pending Renewal Status

## Information Gathered
- The project has lydo_staff blades: dashboard.blade.php, screening.blade.php, renewal.blade.php, settings.blade.php, disbursement.blade.php
- Each blade has notification bell with count, dropdown, and sound using /notification/blade.wav
- Notifications are for approved initial screenings and submitted renewals
- Pending renewals are tracked separately with $pendingRenewals variable
- Sound currently plays when there are any notifications and not viewed
- Bell count is hidden when clicked by setting localStorage 'notificationsViewed' to true
- Real-time updates are handled via Laravel Echo for applicant.updated and renewal.updated

## Plan
- Modify the sound condition in all lydo_staff blade files to play only when there are approved initial screenings AND pending renewals
- Change the @if condition from $notifications->count() > 0 to $notifications->where('initial_screening', 'Approved')->count() > 0 && $pendingRenewals > 0
- Ensure the notification dropdown and count logic remains unchanged
- Apply to all 5 blade files: dashboard, screening, renewal, settings, disbursement

## Dependent Files to be edited
- resources/views/lydo_staff/dashboard.blade.php
- resources/views/lydo_staff/screening.blade.php
- resources/views/lydo_staff/renewal.blade.php
- resources/views/lydo_staff/settings.blade.php
- resources/views/lydo_staff/disbursement.blade.php

## Followup steps
- Test the notification bell functionality after changes
- Verify sound plays only when both conditions are met
- Ensure no damage to existing code

<ask_followup_question>
<question>Do you approve this plan to modify the notification bell sound condition in all lydo_staff blade files?</question>
</ask_followup_question>
