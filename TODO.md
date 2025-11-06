TO-DO LIST

Create a new migration to add the application_requirement column in the tbl_applicant table.

Run the migration to update the database structure.

Update the controller method (e.g., applicants() in LydoAdminController) to include the new column in the query result.

Edit the applicants view file (/lydo_admin/applicants.blade.php) to:

Add a new column header labeled “Application Requirement”.

Add a modal button in each applicant’s row that opens a modal showing their 5 submitted application requirements.

Design the modal layout that displays the list of 5 submitted requirements (e.g., Barangay Indigency, Form 138, etc.).

Connect the modal button to dynamically load each applicant’s requirements (either through a controller route or using AJAX if you want real-time loading).

Add proper validation to ensure applicants’ requirements are recorded or updated correctly.

Test the modal display — make sure the button shows the correct 5 requirements per applicant.

Polish the UI (optional): add icons, tooltips, or color indicators for missing or complete requirements.

Re-check database and interface integration to confirm data consistency and correct linking between applicant and requirements.