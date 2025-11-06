O-DO LIST: Add Approve & Reject Function in Modal (/mayor_staff/status)

Locate the modal inside /mayor_staff/status and ensure there are two buttons — Approve and Reject.

Link both buttons to a JavaScript function that will handle their specific actions.

For the Reject button:

When clicked, trigger a SweetAlert popup that includes input boxes for entering the rejection reason.

On submit, send the rejection reason and application ID to the backend via updateStatus.

In the backend, update the applicant’s status to Rejected.

Send an email notification to the applicant containing the rejection message and reason.

Show a confirmation SweetAlert that the rejection was processed successfully.

For the Approve button:
When clicked, trigger a confirmation SweetAlert asking if the user is sure about approving.

On confirmation, call updateStatus to update the applicant’s status to Approved.

In the backend, send both an SMS and an email to the applicant notifying them of the approval.

Include in the email a link for creating their scholar account.

Show a success SweetAlert confirming the approval was sent and status updated.

Ensure backend email and SMS functions are properly connected and tested.

Verify that the status updates (Approved/Rejected) reflect immediately in the table or list view after action.

Perform final testing:

Try approving and rejecting multiple applicants.

Confirm that emails, SMS, and status changes are working correctly.