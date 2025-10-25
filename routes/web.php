<?php

// Routes for the LYDO Scholarship Application
use App\Http\Controllers\MayorStaffController;
use App\Http\Controllers\LydoAdminController;
use App\Http\Controllers\LydoStaffController;
use App\Http\Controllers\LydopersController;
use App\Http\Controllers\ScholarController;

Route::get('/reset-password', function () {return redirect()->route('login');});
Route::get('/', [LydopersController::class, 'showfrontpage'])->name('home');
Route::get('/login', [LydopersController::class, 'showLoginForm'])->name('login');
Route::get('/registration', [LydopersController::class, 'showregistrationForm'])->name('lydopers.registration');
Route::post('/register-personnel', [LydopersController::class, 'store'])->name('lydopers.register');
Route::get('/applicant-registration', [ScholarController::class, 'showApplicantsRegForm'])->name('applicants.registration');
Route::post('/register-applicant', [ScholarController::class, 'storeApplicantsReg'])->name('applicants.register');
Route::get('/check-applicant-email', [ScholarController::class, 'checkEmail'])->name('check.applicant.email');
Route::post('/check-scholar-username', [ScholarController::class, 'checkUsername'])->name('check.scholar.username');
Route::post('/check-scholar-id', [ScholarController::class, 'checkScholarId'])->name('check.scholar.id');
Route::post('/login', [LydopersController::class, 'login'])->name('login.submit');
Route::get('/scholar/login', [ScholarController::class, 'showLoginForm'])->name('scholar.login');
Route::get('/scholar/scholar_registration', [ScholarController::class, 'showScholarRegistration'])->name('scholar.scholar_reg');
Route::post('/scholar/login', [ScholarController::class, 'login'])->name('scholar.login.submit');
Route::post('/scholar/register', [ScholarController::class, 'registerScholar'])->name('scholar.register');
Route::get('/scholar/forgot-password', [ScholarController::class, 'showForgotPasswordForm'])->name('scholar.forgot-password');
 Route::get('/scholar/announcements', [ScholarController::class, 'announcements'])->name('scholar.announcements');
Route::post('/scholar/forgot-password', [ScholarController::class, 'sendResetLink'])->name('scholar.password.email');
Route::post('/scholar/verify-otp', [ScholarController::class, 'verifyOtp'])->name('scholar.password.verifyOtp');
Route::get('/scholar/update-application/{applicant_id}', [ScholarController::class, 'showUpdateApplication'])->name('scholar.showUpdateApplication');
Route::post('/scholar/update-application/{applicant_id}', [ScholarController::class, 'updateApplication'])->name('scholar.updateApplication');
Route::post('/scholar/resend-otp', [ScholarController::class, 'resendOtp'])->name('scholar.password.resendOtp');
Route::get('/scholar/reset-password/{token}', [ScholarController::class, 'showResetForm'])->name('scholar.password.reset');
Route::post('/scholar/reset-password', [ScholarController::class, 'resetPassword'])->name('scholar.password.update');
Route::post('/logout', [LydopersController::class, 'logout'])->name('logout');
Route::post('/check-email', [LydopersController::class, 'checkEmail'])->name('check.email');
Route::post('/check-username', [LydopersController::class, 'checkUsername'])->name('check.username');
Route::get('/forgot-password', [LydopersController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [LydopersController::class, 'sendResetLink'])->name('password.email');
Route::post('/verify-otp', [LydopersController::class, 'verifyOtp'])->name('password.verifyOtp');
Route::post('/resend-otp', [LydopersController::class, 'resendOtp'])->name('password.resendOtp');
Route::get('/reset-password/{token}', [LydopersController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [LydopersController::class, 'resetPassword'])->name('password.update');

Route::middleware(['role:lydo_admin'])->group(function () {
    Route::get('/lydo_admin/dashboard', [LydoAdminController::class, 'index'])->name('LydoAdmin.dashboard');
    Route::get('/lydo_admin/lydo', [LydoAdminController::class, 'lydo'])->name('LydoAdmin.lydo');
    Route::get('/lydo_admin/mayor', [LydoAdminController::class, 'mayor'])->name('LydoAdmin.mayor');
    Route::get('/lydo_admin/scholar', [LydoAdminController::class, 'scholar'])->name('LydoAdmin.scholar');
    Route::get('/lydo_admin/announcement', [LydoAdminController::class, 'announcement'])->name('LydoAdmin.announcement');
    Route::post('/lydo_admin/store-announcement', [LydoAdminController::class, 'storeAnnouncement'])->name('LydoAdmin.storeAnnouncement');
    Route::delete('/lydo_admin/delete-announcement/{tbl_announce_id}', [LydoAdminController::class, 'deleteAnnouncement'])->name('LydoAdmin.deleteAnnouncement');
    Route::post('/lydo_admin/send-email', [LydoAdminController::class, 'sendEmail'])->name('LydoAdmin.sendEmail');
    Route::get('/lydo_admin/disbursement', [LydoAdminController::class, 'disbursement'])->name('LydoAdmin.disbursement'); // New route for disbursement
    Route::get('/lydo_admin/lydo/toggle/{id}', [LydoAdminController::class, 'toggleStatus'])->name('lydo.toggle');
    Route::get('/lydo_admin/mayor/toggle/{id}', [LydoAdminController::class, 'toggleStatus'])->name('mayor.toggle');
    Route::get('/lydo_admin/status', [LydoAdminController::class, 'status'])->name('LydoAdmin.status');
    Route::post('/lydo_admin/update-scholar-status', [LydoAdminController::class, 'updateScholarStatus'])->name('LydoAdmin.updateScholarStatus');
    Route::get('/lydo_admin/settings', [LydoAdminController::class, 'settings'])->name('LydoAdmin.settings');
Route::put('/lydo_admin/announcement/{id}', [LydoAdminController::class, 'updateAnnouncement'])->name('LydoAdmin.updateAnnouncement');
    Route::put('/lydo_admin/update-password', [LydoAdminController::class, 'updatePassword'])->name('LydoAdmin.updatePassword');
    Route::put('/lydo_admin/update-deadlines', [LydoAdminController::class, 'updateDeadlines'])->name('LydoAdmin.updateDeadlines');
    Route::put('/lydo_admin/update-personal-info/{id}', [LydoAdminController::class, 'updatePersonalInfo'])->name('LydoAdmin.updatePersonalInfo');
    Route::get('/lydo_admin/report', [LydoAdminController::class, 'report'])->name('LydoAdmin.report');
    Route::post('/lydo_admin/report', [LydoAdminController::class, 'report'])->name('LydoAdmin.report.post');
    Route::get('/lydo_admin/applicants', [LydoAdminController::class, 'applicants'])->name('LydoAdmin.applicants');
    Route::get('/lydo_admin/get-all-filtered-applicants', [LydoAdminController::class, 'getAllFilteredApplicants'])->name('LydoAdmin.getAllFilteredApplicants');
    Route::get('/lydo_admin/get-all-filtered-scholars', [LydoAdminController::class, 'getAllFilteredScholars'])->name('LydoAdmin.getAllFilteredScholars');
    Route::post('/lydo_admin/get-scholar-names', [LydoAdminController::class, 'getScholarNames'])->name('LydoAdmin.getScholarNames');
    Route::post('/lydo_admin/get-scholar-names', [LydoAdminController::class, 'getScholarNames'])->name('LydoAdmin.getScholarNames');
    Route::post('/lydo_admin/create-disbursement', [LydoAdminController::class, 'createDisbursement'])->name('LydoAdmin.createDisbursement');
    Route::post('/lydo_admin/send-email-to-applicants', [LydoAdminController::class, 'sendEmailToApplicants'])->name('LydoAdmin.sendEmailToApplicants');
    Route::post('/lydo_admin/send-sms-to-applicants', [SmsController::class, 'sendSmsToApplicants'])->name('LydoAdmin.sendSmsToApplicants');
    Route::get('/lydo_admin/report/pdf/scholars', [LydoAdminController::class, 'generateScholarsPdf'])->name('LydoAdmin.report.pdf.scholars');
    Route::get('/lydo_admin/report/pdf/scholars-by-barangay', [LydoAdminController::class, 'generateScholarsPdfByBarangay'])->name('LydoAdmin.report.pdf.scholarsByBarangay');
    Route::get('/lydo_admin/report/pdf/applicants', [LydoAdminController::class, 'generateApplicantsPdf'])->name('LydoAdmin.report.pdf.applicants');
    Route::get('/lydo_admin/report/pdf/renewal', [LydoAdminController::class, 'generateRenewalPdf'])->name('LydoAdmin.report.pdf.renewal');
    Route::get('/lydo_admin/report/pdf/summary', [LydoAdminController::class, 'generateSummaryPdf'])->name('LydoAdmin.report.pdf.summary');
    Route::get('/lydo_admin/get-scholars-by-barangay', [LydoAdminController::class, 'getScholarsByBarangay'])->name('LydoAdmin.getScholarsByBarangay');
    Route::get('/lydo_admin/get-scholars-with-disbursement', [LydoAdminController::class, 'getScholarsWithDisbursement'])->name('LydoAdmin.getScholarsWithDisbursement');
    Route::get('/lydo_admin/disbursement-pdf', [LydoAdminController::class, 'generateDisbursementPdf'])->name('LydoAdmin.disbursement.pdf');



});

Route::middleware(['role:lydo_staff'])->group(function () {
    Route::get('/lydo_staff/dashboard', [LydoStaffController::class, 'index'])->name('LydoStaff.dashboard');
    Route::get('/lydo_staff/screening', [LydoStaffController::class, 'screening'])->name('LydoStaff.screening');
    Route::put('/remarks/{id}', [LydoStaffController::class, 'updateRemarks'])->name('updateRemarks');
    Route::put('/remarksUpdate/{id}', [LydoStaffController::class, 'remarksUpdate'])->name('remarks.update');
    Route::post('/update-remarks', [LydoStaffController::class, 'updateApplicantsRemarks'])->name('updateApplicantsRemarks');
    Route::get('/lydo_staff/renewal', [LydoStaffController::class, 'renewal'])->name('LydoStaff.renewal');
    Route::post('/lydo_staff/renewal/update/{scholarId}', [LydoStaffController::class, 'updateStatus']);
    Route::get('/renewals/{id}/requirements', [RenewalController::class, 'getRequirements']);
    Route::post('/lydo_staff/renewal/{id}/update-status', [LydoStaffController::class, 'updateRenewalStatus'])->name('renewal.updateStatus');
    Route::get('/reviewed-applicants/pdf', [LydoStaffController::class, 'reviewedApplicantsPdf'])->name('LydoStaff.reviewedApplicantsPdf');
    Route::get('/renewal-applications/pdf', [LydoStaffController::class, 'renewalApplicationsPdf'])->name('LydoStaff.renewalApplicationsPdf');
    Route::get('/lydo_staff/disbursement', [LydoStaffController::class, 'disbursement'])->name('LydoStaff.disbursement');
    Route::post('/lydo_staff/sign-disbursement/{disburse_id}', [LydoStaffController::class, 'signDisbursement'])->name('LydoStaff.signDisbursement');
    Route::get('/lydo_staff/settings', [LydoStaffController::class, 'settings'])->name('LydoStaff.settings');
    Route::put('/lydo_staff/update/{id}', [LydoStaffController::class, 'updateStaff'])->name('lydo_staff.update');
    Route::put('/lydo_staff/update-applicant/{id}', [LydoStaffController::class, 'updateApplicant'])->name('lydo_staff.updateApplicant');
    Route::post('/lydo_staff/send-email', [LydoStaffController::class, 'sendEmail'])->name('send.email');
    Route::put('/lydo_staff/update-password', [LydoStaffController::class, 'updatePassword'])->name('lydo_staff.updatePassword');
    Route::post('/lydo_staff/mark-notifications-viewed', [LydoStaffController::class, 'markNotificationsViewed'])->name('LydoStaff.markNotificationsViewed');
    Route::get('/lydo_staff/latest-applicants', [LydoStaffController::class, 'getLatestApplicants'])->name('LydoStaff.getLatestApplicants');
    Route::get('/lydo_staff/latest-renewals', [LydoStaffController::class, 'getLatestRenewals'])->name('LydoStaff.getLatestRenewals');
    Route::get('/lydo_staff/latest-disbursements', [LydoStaffController::class, 'getLatestDisbursements'])->name('LydoStaff.getLatestDisbursements');
    Route::get('/lydo_staff/sse-applicants', [LydoStaffController::class, 'sse'])->name('LydoStaff.sse');
    Route::get('/lydo_staff/reports', [LydoStaffController::class, 'reports'])->name('LydoStaff.reports');

});

// Mayor Staff Routes - Only accessible by mayor_staff role
Route::middleware(['role:mayor_staff'])->group(function () {
    Route::get('/mayor_staff/dashboard', [MayorStaffController::class, 'index']) ->name('MayorStaff.dashboard');
    Route::get('/application-personnel/{id}/requirements', [MayorStaffController::class, 'getApplicationRequirements'])->name('application.requirements');
    Route::get('/mayor_staff/application', [MayorStaffController::class, 'application'])->name('MayorStaff.application');
    Route::post('/mayor_staff/application/{id}/approve', [MayorStaffController::class, 'approveApplication'])->name('mayor_staff.approveApplication');
    Route::post('/mayor_staff/application/{id}/reject', [MayorStaffController::class, 'rejectApplication'])->name('mayor_staff.rejectApplication');
    Route::post('/mayor_staff/application/{id}/update-initial-screening', [MayorStaffController::class, 'updateInitialScreening'])->name('application.updateInitialScreening');
    Route::get('/applications/{id}/requirements', [ApplicationController::class, 'getRequirements']);
    Route::get('/mayor_staff/status', [MayorStaffController::class, 'status'])->name('MayorStaff.status');
    Route::post('/mayor_staff/status/{id}', [MayorStaffController::class, 'updateStatus'])->name('MayorStaff.updateStatus');
    Route::get('/mayor_staff/settings', [MayorStaffController::class, 'settings'])->name('MayorStaff.settings');
    Route::put('/mayor_staff/update/{id}', [MayorStaffController::class, 'updatePersonalInfo'])->name('MayorStaff.update');
    Route::put('/mayor_staff/update-password', [MayorStaffController::class, 'updatePassword'])->name('MayorStaff.updatePassword');
    Route::post('/mayor_staff/mark-notifications-viewed', [MayorStaffController::class, 'markNotificationsViewed'])->name('MayorStaff.markNotificationsViewed');
    Route::delete('/mayor_staff/application/{id}', [MayorStaffController::class, 'deleteApplication'])->name('mayor_staff.deleteApplication');
    Route::post('/mayor_staff/send-email', [MayorStaffController::class, 'sendEmail'])->name('mayor_staff.sendEmail');
    Route::get('/mayor_staff/dashboard/updates', [MayorStaffController::class, 'getDashboardUpdates'])->name('MayorStaff.getDashboardUpdates');
    Route::get('/mayor_staff/application/updates', [MayorStaffController::class, 'getApplicationUpdates'])->name('MayorStaff.getApplicationUpdates');
    Route::get('/mayor_staff/status/updates', [MayorStaffController::class, 'getStatusUpdates'])->name('MayorStaff.getStatusUpdates');
    Route::get('/mayor_staff/sse-applicants', [MayorStaffController::class, 'sseApplicants'])->name('MayorStaff.sseApplicants');
    Route::get('/mayor_staff/get-applications-data', [MayorStaffController::class, 'getApplicationsData'])->name('MayorStaff.getApplicationsData');
    Route::get('/mayor_staff/get-application-search-results', [MayorStaffController::class, 'getApplicationSearchResults'])->name('MayorStaff.getApplicationSearchResults');
    Route::post('/mayor_staff/submit-intake-sheet', [MayorStaffController::class, 'submitIntakeSheet'])->name('mayor_staff.submitIntakeSheet');
    Route::get('/mayor_staff/get-document-comments/{id}', [MayorStaffController::class, 'getDocumentComments'])->name('mayor_staff.getDocumentComments');
    Route::post('/mayor_staff/save-document-comment', [MayorStaffController::class, 'saveDocumentComment'])->name('mayor_staff.saveDocumentComment');
    Route::post('/mayor_staff/save-document-status', [MayorStaffController::class, 'saveDocumentStatus'])->name('mayor_staff.saveDocumentStatus');
    Route::post('/mayor_staff/send-document-email', [MayorStaffController::class, 'sendDocumentEmail'])->name('mayor_staff.sendDocumentEmail');
    Route::get('/mayor_staff/welcome', [MayorStaffController::class, 'welcome'])->name('MayorStaff.welcome');

});

// Public routes for intake sheet
Route::get('/intake-sheet/{application_personnel_id}', [MayorStaffController::class, 'showIntakeSheet'])->name('intake_sheet.show');
Route::post('/submit-intake-sheet', [MayorStaffController::class, 'submitIntakeSheetPublic'])->name('intake_sheet.submit');


Route::middleware(['scholar.auth'])->group(function () {

    Route::get('/scholar/renewal_app', [ScholarController::class, 'showRenewalApp'])->name('scholar.renewal_app');
    Route::post('/scholar/submit_renewal', [ScholarController::class, 'submitRenewal'])->name('scholar.submit_renewal');
    Route::get('/scholar/dashboard', [ScholarController::class, 'dashboard'])->name('scholar.dashboard');
    Route::get('/scholar/settings', [ScholarController::class, 'showSettings'])->name('scholar.settings');
    Route::post('/scholar/settings/update', [ScholarController::class, 'updateSettings'])->name('scholar.settings.update');
   });

use App\Http\Controllers\SmsController;
Route::get('/test-sms', [SmsController::class, 'testSend'])->name('test.sms');
