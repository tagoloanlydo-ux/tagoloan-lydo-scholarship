# TODO: Convert Mayor Staff Application Tables to DataTables with Real-Time Filtering

## Tasks
- [x] Update `resources/views/mayor_staff/application.blade.php`:
  - Add DataTables JS script
  - Add ID "reviewedTable" to the reviewed applications table
  - Remove `oninput` and `onchange` from forms to prevent page refresh
  - Add IDs to search inputs and barangay selects for binding

- [x] Update `public/js/mayor_staff_application.js`:
  - Initialize DataTables for both #pendingTable and #reviewedTable when tabs are shown
  - Bind search inputs to table.search().draw()
  - Bind barangay selects to table.column(2).search().draw()
  - Add showTable() and showList() functions for tab switching with persistence

## Followup
- [x] Test real-time filtering by name and barangay without page refresh
