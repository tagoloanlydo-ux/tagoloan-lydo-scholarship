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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::apiResource('/applicants', ApplicantController::class);
Route::apiResource('/applications', ApplicationController::class);
Route::apiResource('/scholars', ScholarController::class);
Route::apiResource('/renewals', RenewalController::class);
Route::apiResource('/disbursements', DisbursementController::class);
Route::apiResource('/announcements', AnnouncementController::class);
Route::apiResource('/reports', ReportController::class);
Route::apiResource('/notifications', NotificationController::class);
Route::apiResource('/admins', AdminController::class);

?>