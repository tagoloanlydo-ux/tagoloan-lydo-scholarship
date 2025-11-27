<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ScholarController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\DisbursementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RenewalController;
use App\Http\Controllers\EmailController;

use App\Http\Controllers\MayorApplicationController;
use App\Http\Controllers\MayorStaffController;

// API Controllers
use App\Http\Controllers\API\AuthController as ApiAuthController;
use App\Http\Controllers\API\ScholarController as ApiScholarController;
use App\Http\Controllers\API\ApplicationController as ApiApplicationController;
use App\Http\Controllers\API\AdminController as ApiAdminController;
use App\Http\Controllers\API\NotificationController as ApiNotificationController;
use App\Http\Controllers\API\ReportController as ApiReportController;
use App\Http\Controllers\API\DisbursementController as ApiDisbursementController;
use App\Http\Controllers\API\ApplicantController as ApiApplicantController;
use App\Http\Controllers\API\RenewalController as ApiRenewalController;
use App\Http\Controllers\API\AnnouncementController as ApiAnnouncementController;

// ----------------------- MOBILE API ROUTES -----------------------

// ----------------------- PUBLIC DATA ENDPOINTS (No Auth Required for Now) -----------------------
Route::get('/reports', [ApiReportController::class, 'index']);
Route::get('/staff', [ApiAdminController::class, 'index']);

    // ----------------------- PUBLIC ROUTES -----------------------
    Route::post('/auth/login', [ApiAuthController::class, 'login']);
    Route::post('/auth/register', [ApiAuthController::class, 'register']);
    Route::post('/auth/send-otp', [ApiAuthController::class, 'sendOtp']);
    Route::post('/auth/verify-otp', [ApiAuthController::class, 'verifyOtp']);
    Route::post('/auth/reset-password', [ApiAuthController::class, 'resetPassword']);

    // Public application submission
    Route::post('/applications/submit', [ApiApplicationController::class, 'submit']);
    Route::get('/applications/status/{applicantId}', [ApiApplicationController::class, 'status']);
    Route::get('/applications/{applicationId}/requirements', [ApiApplicationController::class, 'requirements']);
    Route::post('/applications/{applicationId}/upload-document', [ApiApplicationController::class, 'uploadDocument']);

    // ----------------------- PROTECTED ROUTES (Require Authentication) -----------------------
    Route::middleware('api.auth')->group(function () {

        // ----------------------- AUTH -----------------------
        Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
        Route::get('/auth/profile', [ApiAuthController::class, 'profile']);

        // ----------------------- SCHOLAR ROUTES -----------------------
        Route::middleware('api.role:scholar')->prefix('scholar')->group(function () {
            Route::get('/dashboard', [ApiScholarController::class, 'dashboard']);
            Route::get('/profile', [ApiScholarController::class, 'profile']);
            Route::put('/profile', [ApiScholarController::class, 'updateProfile']);
            Route::post('/renewals/submit', [ApiScholarController::class, 'submitRenewal']);
            Route::get('/renewals/history', [ApiScholarController::class, 'renewalHistory']);
            Route::get('/applications/status', [ApiScholarController::class, 'applicationStatus']);
            Route::get('/announcements', [ApiScholarController::class, 'announcements']);
        });

        // ----------------------- ADMIN ROUTES -----------------------
        Route::middleware('api.role:lydo_admin')->prefix('admin')->group(function () {
            Route::get('/dashboard', [ApiAdminController::class, 'dashboard']);
            Route::get('/applicants', [ApiAdminController::class, 'applicants']);
            Route::get('/applicants/{applicantId}', [ApiAdminController::class, 'applicantDetails']);
            Route::put('/applications/{applicationId}/status', [ApiAdminController::class, 'updateApplicationStatus']);
            Route::get('/scholars', [ApiAdminController::class, 'scholars']);
            Route::put('/scholars/{scholarId}/status', [ApiAdminController::class, 'updateScholarStatus']);
            Route::get('/renewals', [ApiAdminController::class, 'renewals']);
            Route::put('/renewals/{renewalId}/status', [ApiAdminController::class, 'updateRenewalStatus']);
            Route::post('/announcements', [ApiAdminController::class, 'createAnnouncement']);
            Route::get('/disbursements', [ApiAdminController::class, 'disbursements']);
            Route::post('/disbursements', [ApiAdminController::class, 'createDisbursement']);
            Route::put('/disbursements/{disbursementId}/status', [ApiAdminController::class, 'updateDisbursementStatus']);
        });

        // ----------------------- STAFF ROUTES -----------------------
        Route::middleware('api.role:lydo_staff')->prefix('staff')->group(function () {
            Route::get('/', [ApiAdminController::class, 'index']);
            Route::get('/dashboard', [ApiAdminController::class, 'dashboard']);
            Route::get('/applicants', [ApiAdminController::class, 'applicants']);
            Route::put('/applications/{applicationId}/status', [ApiAdminController::class, 'updateApplicationStatus']);
        });

        // ----------------------- MAYOR STAFF ROUTES -----------------------
        Route::middleware('api.role:mayor_staff')->prefix('mayor-staff')->group(function () {
            Route::get('/dashboard', [ApiAdminController::class, 'dashboard']);
            Route::get('/applicants', [ApiAdminController::class, 'applicants']);
            Route::get('/applications', [ApiAdminController::class, 'applicants']);
            Route::get('/status', [ApiAdminController::class, 'applicants']); // For status updates
            Route::put('/applications/{applicationId}/status', [ApiAdminController::class, 'updateApplicationStatus']);
            Route::put('/applications/{applicationId}', [ApiAdminController::class, 'updateApplicationStatus']); // For initial screening remarks
            Route::post('/applications/{applicationId}/send-email', [ApiAdminController::class, 'sendEmail']);
            Route::post('/applications/{applicationId}/send-sms', [ApiNotificationController::class, 'sendSms']);
            Route::delete('/applications/{applicationId}', [ApiAdminController::class, 'deleteApplication']);
            Route::get('/applications/test', [ApiAdminController::class, 'testEndpoint']);
            Route::post('/applications/send-bulk-email', [ApiAdminController::class, 'sendBulkEmail']);
            Route::post('/applications/send-bulk-sms', [ApiNotificationController::class, 'sendBulkSms']);
        });

        // ----------------------- NOTIFICATIONS -----------------------
        Route::prefix('notifications')->group(function () {
            Route::get('/announcements', [ApiNotificationController::class, 'announcements']);
            Route::post('/announcements', [ApiNotificationController::class, 'createAnnouncement'])->middleware('api.role:lydo_admin');
            Route::put('/announcements/{announcementId}', [ApiNotificationController::class, 'updateAnnouncement'])->middleware('api.role:lydo_admin');
            Route::delete('/announcements/{announcementId}', [ApiNotificationController::class, 'deleteAnnouncement'])->middleware('api.role:lydo_admin');
            Route::post('/send-email', [ApiNotificationController::class, 'sendEmail'])->middleware('api.role:lydo_admin');
            Route::post('/send-sms', [ApiNotificationController::class, 'sendSms'])->middleware('api.role:lydo_admin');
            Route::post('/mark-viewed', [ApiNotificationController::class, 'markAsViewed']);
        });

        // ----------------------- REPORTS -----------------------
        Route::prefix('reports')->group(function () {
            Route::get('/', [ApiReportController::class, 'index']);
            Route::get('/statistics', [ApiReportController::class, 'statistics']);
            Route::get('/filter-options', [ApiReportController::class, 'filterOptions']);
            Route::get('/applicants/export', [ApiReportController::class, 'exportApplicants']);
            Route::get('/applicants/pdf', [ApiReportController::class, 'applicantsReport'])->middleware('api.role:lydo_admin');
            Route::get('/scholars/pdf', [ApiReportController::class, 'scholarsReport'])->middleware('api.role:lydo_admin');
            Route::get('/disbursements/pdf', [ApiReportController::class, 'disbursementsReport'])->middleware('api.role:lydo_admin');
            Route::get('/renewals/pdf', [ApiReportController::class, 'renewalsReport'])->middleware('api.role:lydo_admin');
            Route::get('/summary/pdf', [ApiReportController::class, 'summaryReport'])->middleware('api.role:lydo_admin');
        });

    // ----------------------- DISBURSEMENTS -----------------------
    Route::prefix('disbursements')->group(function () {
        Route::get('/', [ApiDisbursementController::class, 'index']);
        Route::get('/{disbursementId}', [ApiDisbursementController::class, 'show']);
        Route::post('/', [ApiDisbursementController::class, 'store'])->middleware('api.role:lydo_admin');
        Route::put('/{disbursementId}', [ApiDisbursementController::class, 'update'])->middleware('api.role:lydo_admin');
        Route::delete('/{disbursementId}', [ApiDisbursementController::class, 'destroy'])->middleware('api.role:lydo_admin');
        Route::get('/{disbursementId}/voucher', [ApiDisbursementController::class, 'generateVoucher']);
        Route::get('/scholar/{scholarId}', [ApiDisbursementController::class, 'scholarDisbursements']);
        Route::get('/pending/count', [ApiDisbursementController::class, 'pendingCount']);
        Route::post('/bulk-update-status', [ApiDisbursementController::class, 'bulkUpdateStatus'])->middleware('api.role:lydo_admin');
        Route::get('/report/generate', [ApiDisbursementController::class, 'generateReport']);
        Route::get('/statistics/overview', [ApiDisbursementController::class, 'statistics']);
    });

        // ----------------------- APPLICANTS -----------------------
        Route::get('/applicants', [ApiApplicantController::class, 'index']);
        Route::post('/applicants', [ApiApplicantController::class, 'store']);
        Route::get('/applicants/{id}', [ApiApplicantController::class, 'show']);
        Route::put('/applicants/{id}', [ApiApplicantController::class, 'update']);
        Route::delete('/applicants/{id}', [ApiApplicantController::class, 'destroy']);

        // ----------------------- SCHOLARS -----------------------
        Route::get('/scholars', [ApiScholarController::class, 'index']);
        Route::post('/scholars', [ApiScholarController::class, 'store']);
        Route::get('/scholars/{id}', [ApiScholarController::class, 'show']);
        Route::put('/scholars/{id}', [ApiScholarController::class, 'update']);
        Route::delete('/scholars/{id}', [ApiScholarController::class, 'destroy']);

        // ----------------------- RENEWALS -----------------------
        Route::get('/renewals', [ApiRenewalController::class, 'index']);
        Route::post('/renewals', [ApiRenewalController::class, 'store']);
        Route::get('/renewals/{id}', [ApiRenewalController::class, 'show']);
        Route::put('/renewals/{id}', [ApiRenewalController::class, 'update']);
        Route::delete('/renewals/{id}', [ApiRenewalController::class, 'destroy']);
        Route::get('/renewals/pending/count', [ApiRenewalController::class, 'pendingCount']);

        // ----------------------- ANNOUNCEMENTS -----------------------
        Route::get('/announcements', [ApiAnnouncementController::class, 'index']);
        Route::post('/announcements', [ApiAnnouncementController::class, 'store']);
        Route::get('/announcements/{id}', [ApiAnnouncementController::class, 'show']);
        Route::put('/announcements/{id}', [ApiAnnouncementController::class, 'update']);
        Route::delete('/announcements/{id}', [ApiAnnouncementController::class, 'destroy']);

    });

// ----------------------- LEGACY ROUTES REMOVED -----------------------
// Legacy routes have been removed to avoid confusion. Use API routes instead.

// ----------------------- PUBLIC APPLICATION SUBMISSION -----------------------
Route::post('/applications', [ApplicantController::class, 'store']); // Allow public submission
Route::post('/mayor/applications', [MayorApplicationController::class, 'store']); // Allow public submission

// ----------------------- API TEST -----------------------
Route::get('/test', function() {
    return response()->json(['message' => 'API is working!']);
});

// ----------------------- SETUP TEST USER -----------------------
Route::post('/setup-test-user', function() {
    $user = \App\Models\User::create([
        'name' => 'Test Mayor',
        'email' => 'mayor@test.com',
        'password' => bcrypt('password'),
        'role' => 'mayor',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Test user created',
        'user' => $user
    ]);
});

// ----------------------- PROTECTED ROUTES (Require Authentication) -----------------------
Route::middleware('auth:api')->group(function () {

    // ----------------------- AUTH -----------------------
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // ----------------------- MAYOR APPLICATIONS -----------------------
    Route::prefix('mayor')->group(function () {
        Route::get('/applications', [MayorApplicationController::class, 'index']);
        Route::post('/applications', [MayorApplicationController::class, 'store']);
        Route::post('/applications/status/{id}', [MayorApplicationController::class, 'updateStatus']);
        Route::get('/applications/{id}', [MayorApplicationController::class, 'show']);
        Route::put('/applications/{id}', [MayorApplicationController::class, 'update']);
        Route::delete('/applications/{id}', [MayorApplicationController::class, 'destroy']);
    });

    // ----------------------- MAYOR STAFF API ROUTES (Mobile App) -----------------------
    Route::prefix('mayor')->group(function () {
        // Dashboard routes
        Route::get('/dashboard', [MayorStaffController::class, 'index']);


        // Application management routes
        Route::get('/applications', [MayorStaffController::class, 'application']);
        Route::get('/application/updates', [MayorStaffController::class, 'getApplicationUpdates']);
        Route::post('/applications/{id}/approve', [MayorStaffController::class, 'approveApplication']);
        Route::post('/applications/{id}/reject', [MayorStaffController::class, 'rejectApplication']);
        Route::post('/applications/{id}/update-initial-screening', [MayorStaffController::class, 'updateInitialScreening']);
        Route::patch('/applications/{id}/edit-initial-screening', [MayorStaffController::class, 'editInitialScreening']);
        Route::delete('/applications/{id}', [MayorStaffController::class, 'deleteApplication']);
        Route::get('/applications/{id}/requirements', [MayorStaffController::class, 'getRequirements']);

        // Status management routes
        Route::get('/status', [MayorStaffController::class, 'status']);
        Route::get('/status/updates', [MayorStaffController::class, 'getStatusUpdates']);
        Route::post('/applications/{id}/update-status', [MayorStaffController::class, 'updateStatus']);

        // Email and SMS routes
        Route::post('/send-email', [MayorStaffController::class, 'sendEmail']);

        // Settings routes
        Route::get('/settings', [MayorStaffController::class, 'settings']);
        Route::put('/settings/personal-info/{id}', [MayorStaffController::class, 'updatePersonalInfo']);
        Route::put('/settings/password', [MayorStaffController::class, 'updatePassword']);
        Route::post('/notifications/viewed', [MayorStaffController::class, 'markNotificationsViewed']);

        // Report routes
        Route::get('/report', [MayorStaffController::class, 'report']);
        Route::get('/report/print', [MayorStaffController::class, 'printReport']);
        Route::get('/report/print-status', [MayorStaffController::class, 'printStatusReport']);

        // SSE routes
        Route::get('/sse/applicants', [MayorStaffController::class, 'sseApplicants']);
    });

    // ----------------------- ANNOUNCEMENTS -----------------------
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/scholars', [AnnouncementController::class, 'scholars']);
    Route::get('/announcements/applicants', [AnnouncementController::class, 'applicants']);
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);

    // ----------------------- SCHOLARS -----------------------
    Route::get('/scholars', [ScholarController::class, 'index']);
    Route::get('/scholars/count', [ScholarController::class, 'count']);
    Route::get('/scholars/inactive/count', [ScholarController::class, 'inactiveCount']);
    Route::post('/scholars', [ScholarController::class, 'store']);
    Route::put('/scholars/{id}', [ScholarController::class, 'update']);
    Route::put('/scholars/{id}/profile', [ScholarController::class, 'updateProfile']);
    Route::put('/scholars/{id}/status', [ScholarController::class, 'updateStatus']);
    Route::delete('/scholars/{id}', [ScholarController::class, 'destroy']);

    // ----------------------- APPLICANTS -----------------------
    Route::get('/applicants', [ApplicantController::class, 'index']);
    Route::get('/applicants/meta', [ApplicantController::class, 'meta']);
    Route::get('/applicants/distribution/barangay', [ApplicantController::class, 'distributionByBarangay']);
    Route::get('/applicants/distribution/school', [ApplicantController::class, 'distributionBySchool']);
    Route::post('/applicants', [ApplicantController::class, 'store']);
    Route::put('/applicants/{id}', [ApplicantController::class, 'update']);
    Route::delete('/applicants/{id}', [ApplicantController::class, 'destroy']);

    // ----------------------- DISBURSEMENTS -----------------------
    Route::get('/disbursements', [DisbursementController::class, 'index']);
    Route::get('/disbursements/pending/count', [DisbursementController::class, 'pendingCount']);
    Route::post('/disbursements', [DisbursementController::class, 'store']);
    Route::put('/disbursements/{id}', [DisbursementController::class, 'update']);
    Route::delete('/disbursements/{id}', [DisbursementController::class, 'destroy']);

    // ----------------------- RENEWALS -----------------------
    Route::get('/renewals', [RenewalController::class, 'index']);
    Route::get('/renewals/pending/count', [RenewalController::class, 'pendingCount']);
    Route::post('/renewals', [RenewalController::class, 'store']);
    Route::put('/renewals/{id}', [RenewalController::class, 'update']);
    Route::delete('/renewals/{id}', [RenewalController::class, 'destroy']);

    // ----------------------- EMAIL -----------------------
    Route::post('/send-email', [EmailController::class, 'send']);

    // ----------------------- SMS -----------------------
    Route::post('/send-sms', [EmailController::class, 'sendSms']);

});

// Public routes (no auth required)
Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');
