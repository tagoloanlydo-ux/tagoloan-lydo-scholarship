# TODO: Add Application Requirements Column and Modal

## Tasks
- [x] Add "Application Requirement" column header to the applicants table
- [x] Add "View Requirements" button cell in the table body loop
- [ ] Add Bootstrap modal for each applicant displaying 5 hardcoded requirements
- [ ] Update table colspan from 7 to 8 for empty state
- [ ] Test modal functionality in browser

## Files to Edit
- resources/views/lydo_admin/applicants.blade.php

## Notes
- Use unique modal IDs based on applicant_id (requirementsModal{{ $applicant->applicant_id }})
- Use Bootstrap modal attributes: data-bs-toggle="modal" data-bs-target="#requirementsModal{{ $applicant->applicant_id }}"
- Hardcode 5 application requirements in each modal
