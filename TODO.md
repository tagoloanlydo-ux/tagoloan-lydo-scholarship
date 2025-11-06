✅ **TO DO: Add “View Document” feature in lydo_admin/scholar**

1. **Database:**

   * Ensure `tbl_renewal` contains columns: `document_1`, `document_2`, `document_3`, `academic_year`, `semester`.

2. **Controller (LydoAdminController.php):**

   * Fetch scholars with their renewal records and document paths.
   * Pass them to the `lydo_admin.scholar` view.

3. **Blade View (lydo_admin/scholar.blade.php):**

   * Add a new **“Documents” column** in the scholars table.
   * Insert a **“View Documents” button** that triggers a modal per scholar.

4. **Modal:**

   * Inside the modal, show scholar’s **academic year** and **semester**.
   * Display 3 document slots (Document 1, Document 2, Document 3).
   * Use `<iframe>` or a download link to preview each file directly inside the modal.

5. **File Path:**

   * Access each document using `asset('storage/document/'.$filename)`.

6. **UI:**

   * Make the modal responsive and scrollable for better readability.

7. **Test:**

   * Confirm each scholar’s 3 renewal documents open correctly in the modal by academic year and semester.
