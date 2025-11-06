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

// Wrap all routes in staging prefix to match Flutter app expectations
Route::prefix('staging')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/login', [AuthController::class, 'login']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
    });

    Route::apiResource('/applicants', ApplicantController::class);
    Route::apiResource('/applications', ApplicationController::class);
    Route::apiResource('/scholars', ScholarController::class);
    Route::apiResource('/renewals', RenewalController::class);
    Route::apiResource('/disbursements', DisbursementController::class);
    Route::apiResource('/announcements', AnnouncementController::class);
    Route::apiResource('/reports', ReportController::class);
    Route::apiResource('/notifications', NotificationController::class);
    Route::apiResource('/admins', AdminController::class);
    Route::apiResource('/application-personnels', ApplicationPersonnelController::class);

    // Public scholar announcements route (no auth required)
    Route::get('/scholar/announcements', [AnnouncementController::class, 'getScholarAnnouncements']);

    // Scholar-specific routes
    Route::prefix('scholar')->middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        // Route::get('/announcements', [AnnouncementController::class, 'getScholarAnnouncements']); // Moved to public
    });
});

?>