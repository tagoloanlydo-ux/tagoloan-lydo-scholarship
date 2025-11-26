# TODO: Fix "Select All" to work across all pages/filters in /lydo_admin/applicants

## Current Work
Fix the "Select All" functionality in resources/views/lydo_admin/applicants.blade.php so it selects ALL applicants across ALL pages and filters (not just visible current page). Use Sets to track selected applicant_ids globally. Update renders, checkboxes, actions (copy/email/sms) to use full selections. No backend changes.

## Steps (Approved Plan Breakdown)

### Phase 1: Add Globals & Helper Functions
- [ ] 1. Add global variables: `let selectedMayorIds = new Set(); let selectedLydoIds = new Set(); let currentSelectedTab = null;`
- [ ] 2. Add `updateSelectAllState(tab)` function: Compute filtered selected count, set selectAll.checked/indeterminate.

### Phase 2: Update Render & Checkbox Logic
- [ ] 3. Update `renderMayorApplicantsTable()` & `renderLydoApplicantsTable()`: Checkbox `checked="${selectedXXX.has(applicant.applicant_id) ? 'checked' : ''}"`
- [ ] 4. Update `setupMayorCheckboxes()` & `setupLydoCheckboxes()`:
  - Select All: Toggle ALL filtered ids in Set, then renderTable().
  - Individual: Toggle id in Set, updateSelectAllState(tab).
- [ ] 5. Update `updateButtonVisibility(tab)`: Use `selectedXXX.size > 0`

### Phase 3: Update Actions & Modals
- [ ] 6. Update `copySelectedNames(tab)`: Filter allXXX by Set, map formatName.
- [ ] 7. Update `openEmailModal(tab)` & `openSmsModal(tab)`: Set `currentSelectedTab = tab`, preview from all selectedApps (name — email/phone).
- [ ] 8. Add `openEmailModal` submit handler for `#emailForm` (similar to SMS): Collect selectedEmails from allXXX via Set.

### Phase 4: Integrate with Load/Filter/Pagination
- [ ] 9. Update `loadMayorApplicants()` & `loadLydoReviewedApplicants()`: `selectedXXX.clear()`, call `updateSelectAllState(tab)` after setup.
- [ ] 10. Update `setupMayorFilters()`/`setupLydoFilters()` `applyFilters()`: After render/pagination, `setupXXXCheckboxes(); updateSelectAllState(tab);`

## Followup/Testing
- [ ] Verify: Select All selects all filtered (multi-page), page/filter change preserves/reflects, previews/actions use full list.
- [ ] No breakage: Filters, pagination, PDF, modals.
- [ ] Mark complete, attempt_completion.

**Next Step:** Implement Phase 1.
