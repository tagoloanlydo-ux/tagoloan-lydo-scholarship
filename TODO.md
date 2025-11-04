# TODO: Implement Document Review Feature in Renewal Modal

## Overview
Add Good/Bad buttons for each of the 3 renewal documents (Certificate of Registration, Grade Slip, Barangay Indigency) in the lydo_staff/renewal modal. When clicked, change document color (green for good, red for bad) and icon (check/X), then save remarks to tbl_renewal_comments table.

## Tasks
- [ ] Modify renewal.blade.php modal to add Good/Bad buttons for each document
- [ ] Add JavaScript to handle button clicks, update UI, and send AJAX requests
- [ ] Add controller method in LydoStaffController to save/load comments
- [ ] Update routes to include new endpoints for saving comments
- [ ] Test the functionality: click buttons, verify UI changes, check database saves
- [ ] Ensure existing comments are loaded when modal opens

## Files to Modify
- resources/views/lydo_staff/renewal.blade.php
- app/Http/Controllers/LydoStaffController.php
- routes/web.php (if needed)

## Database
- Use existing tbl_renewal_comments table
- Fields: renewal_comment_id, renewal_id, lydopers_id, comment (good/bad), document_type (cert_of_reg/grade_slip/brgy_indigency)
