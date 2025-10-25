{{-- Wrapper view to satisfy legacy view path `intake_sheet.form`.
     This simply includes the existing Applicants/intakesheet.blade.php template so
     the controller that calls `view('intake_sheet.form', ...)` keeps working.
--}}

@include('Applicants.intakesheet')
