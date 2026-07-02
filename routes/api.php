<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TicketCategoryController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\CheckInController;
use App\Http\Controllers\Api\LuckyDrawController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\SeatController;
use App\Http\Controllers\Api\UserNotificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// PUBLIC ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/forgot-password', [\App\Http\Controllers\Api\PasswordResetController::class, 'forgotPassword']);
Route::post('/verify-otp', [\App\Http\Controllers\Api\PasswordResetController::class, 'verifyOtp']);
Route::post('/reset-password', [\App\Http\Controllers\Api\PasswordResetController::class, 'resetPassword']);


// PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {

    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);

    // USER LOGIN INFO
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/profile', [\App\Http\Controllers\Api\UserProfileController::class, 'update']);

    // EVENT CRUD (READ ONLY FOR ALL AUTHENTICATED USERS)
    Route::apiResource('events', EventController::class)->only(['index', 'show']);

    // TICKET CATEGORY CRUD (READ ONLY FOR ALL AUTHENTICATED USERS)
    Route::apiResource('ticket-categories', TicketCategoryController::class)->only(['index', 'show']);

    // REGISTRATION CRUD
    Route::apiResource('registrations', RegistrationController::class);
    Route::post('/registrations/{registration}/refund', [RegistrationController::class, 'requestRefund']);
    Route::post('/registrations/{registration}/simulate-payment', [RegistrationController::class, 'simulatePayment']);
    Route::post('/registrations/{registration}/upload-payment-proof', [RegistrationController::class, 'uploadPaymentProof']);
    Route::get('/payment-settings', [RegistrationController::class, 'getPaymentSettings']);

    // LUCKY DRAW
    Route::post('/lucky-draw', [LuckyDrawController::class, 'draw'])->middleware('admin.role');
    Route::get('/lucky-draw/my-wins', [LuckyDrawController::class, 'getMyWins']);
    Route::get('/events/{eventId}/winners', [LuckyDrawController::class, 'getWinners']);

    // CERTIFICATE READ
    Route::get('/my-certificates', [CertificateController::class, 'myCertificates']);
    Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download']);

    // SEATS
    Route::get('/events/{eventId}/seats', [SeatController::class, 'index']);

    // NOTIFICATIONS
    Route::get('/notifications', [UserNotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [UserNotificationController::class, 'unreadCount']);
    Route::get('/notifications/{id}', [UserNotificationController::class, 'show']);
    Route::patch('/notifications/{id}/read', [UserNotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [UserNotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/clear-all', [UserNotificationController::class, 'clearAll']);

    // ADMIN WRITE & SECURED ACTIONS (RESTRICTED TO ADMIN ONLY)
    Route::middleware('admin.role')->group(function () {
        // EVENT WRITE ACTIONS
        Route::apiResource('events', EventController::class)->only(['store', 'update', 'destroy']);

        // TICKET CATEGORY WRITE ACTIONS
        Route::apiResource('ticket-categories', TicketCategoryController::class)->only(['store', 'update', 'destroy']);

        // CERTIFICATE GENERATION
        Route::post('/generate-certificate', [CertificateController::class, 'generate']);

        // QR CHECK-IN
        Route::post('/check-in', [CheckInController::class, 'checkIn']);
    });

});