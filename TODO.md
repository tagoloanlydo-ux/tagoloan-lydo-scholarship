# Task: Change Applicant Registration UI to Tabbed Layout

## Overview
Transform the current 2-column layout (Personal Information and Documents) into a responsive tabbed interface with three tabs:
1. Personal Information
2. Educational Attainment
3. Application Requirements

## Steps
- [ ] Modify HTML structure in `resources/views/scholar/applicants_reg.blade.php` to use tabs instead of containers
- [ ] Group form fields into appropriate tabs:
  - Personal Information: Name fields, Gender, Birth Date, Civil Status, Barangay, Email, Contact Number
  - Educational Attainment: School Name, Year Level, Course, Academic Year
  - Application Requirements: All file uploads (Application Letter, Grade Slip, Certificate of Registration, Barangay Indigency, Student ID)
- [ ] Add tab navigation using Tailwind CSS classes
- [ ] Implement JavaScript for tab switching functionality
- [ ] Update CSS in `public/css/application_reg.css` to support tabbed layout and ensure responsiveness
- [ ] Test responsiveness on different screen sizes
- [ ] Verify form submission still works correctly
