<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ✅ API Controllers
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ApplicantController;
use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\ScholarController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\DisbursementController;
use App\Http\Controllers\API\RenewalController;
use App\Http\Controllers\API\AnnouncementController;
use App\Http\Controllers\API\ApplicationPersonnelController;
use App\Http\Controllers\MayorStaffController;

// Wrap all routes in staging prefix to match Flutter app expectations
Route::prefix('staging')->group(function () {
    // Public auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/send-otp', [AuthController::class, 'sendOtp']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // Public routes (no authentication required)
    Route::post('/applicants', [ApplicantController::class, 'store']);

    // Protected routes with Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::prefix('auth')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        // Scholar-specific routes
        Route::prefix('scholar')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::get('/announcements', [AnnouncementController::class, 'getScholarAnnouncements']);
            Route::get('/renewal_app', [RenewalController::class, 'getScholarRenewals']);
            Route::get('/renewals', [RenewalController::class, 'getScholarRenewals']);
            Route::post('/submit_renewal', [RenewalController::class, 'submitScholarRenewal']);
            Route::get('/renewal-history', [RenewalController::class, 'getRenewalHistory']);
            Route::get('/renewal/{renewalId}/details', [RenewalController::class, 'getRenewalDetails']);
        });

     
        // API Resources (excluding applicants store which is now public)
        Route::apiResource('/applicants', ApplicantController::class)->except(['store']);
        Route::apiResource('/applications', ApplicationController::class);
        Route::apiResource('/scholars', ScholarController::class);
        Route::apiResource('/renewals', RenewalController::class);
        Route::apiResource('/disbursements', DisbursementController::class);
        Route::apiResource('/announcements', AnnouncementController::class);
        Route::apiResource('/reports', ReportController::class);
        Route::apiResource('/notifications', NotificationController::class);
        Route::apiResource('/admins', AdminController::class);
        Route::apiResource('/application-personnels', ApplicationPersonnelController::class);

        // Settings endpoint
        Route::get('/settings', [AdminController::class, 'getSettings']);
    });

    // Add this route for debugging
Route::get('/staging/debug/applicants', function () {
    $allApplicants = DB::table('tbl_applicant')->get();
    $allApplications = DB::table('tbl_application')->get();
    $allApplicationPersonnel = DB::table('tbl_application_personnel')->get();
    $mayorStaff = DB::table('tbl_lydopers')->where('lydopers_role', 'mayor_staff')->get();

    return response()->json([
        'applicants_count' => $allApplicants->count(),
        'applications_count' => $allApplications->count(),
        'application_personnel_count' => $allApplicationPersonnel->count(),
        'mayor_staff_count' => $mayorStaff->count(),
        'mayor_staff' => $mayorStaff,
        'recent_applications' => DB::table('tbl_application as app')
            ->join('tbl_applicant as a', 'app.applicant_id', '=', 'a.applicant_id')
            ->leftJoin('tbl_application_personnel as ap', 'app.application_id', '=', 'ap.application_id')
            ->select('a.*', 'app.*', 'ap.*')
            ->orderBy('app.application_id', 'desc')
            ->limit(10)
            ->get()
    ]);
});



});
