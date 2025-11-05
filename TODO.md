# Renewal Document Update Feature Implementation

## Overview
Implement functionality where bad documents in renewal applications trigger an email notification to applicants with staff comments, and allow status reset when documents are updated.

## Current Status
- Document rating system exists (good/bad)
- Update request button appears for bad documents
- Email sending functionality exists
- Status reset method exists but may need integration

## Tasks

### 1. Verify Send Email Button for Bad Documents
- [ ] Confirm button appears in document viewer when document is marked as bad
- [ ] Test button functionality in renewal.js
- [ ] Ensure button only shows for bad documents

### 2. Enhance Email Content
- [x] Verify email includes:
  - Staff comment
  - Document type that needs update
  - Clear instructions for applicant
- [x] Update EmailService.php sendDocumentUpdateRequest method if needed

### 3. Implement Document Status Reset
- [ ] Add functionality to reset bad status to "New" when document is updated
- [ ] Ensure scholars can upload new documents and trigger status reset
- [ ] Integrate markDocumentAsUpdated method properly

### 4. Testing
- [ ] Test complete flow: mark bad → send email → scholar updates → status resets
- [ ] Verify no damage to existing renewal functionality
- [ ] Check email delivery and content

## Files to Modify
- public/js/renewal.js (button logic)
- app/Http/Controllers/RenewalController.php (status reset)
- app/Services/EmailService.php (email content)
- Scholar renewal upload views/controllers (if needed)

## Dependencies
- Ensure scholar portal has document upload functionality
- Email service must be properly configured
