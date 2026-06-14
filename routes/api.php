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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// PUBLIC ROUTES
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {

    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);

    // USER LOGIN INFO
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // EVENT CRUD
    Route::apiResource('events', EventController::class);

    // TICKET CATEGORY CRUD
    Route::apiResource('ticket-categories', TicketCategoryController::class);

    // REGISTRATION CRUD
    Route::apiResource('registrations', RegistrationController::class);
    Route::post('/registrations/{registration}/refund', [RegistrationController::class, 'requestRefund']);

    // QR CHECK-IN
    Route::post('/check-in', [CheckInController::class, 'checkIn']);

    // LUCKY DRAW
    Route::post('/lucky-draw', [LuckyDrawController::class, 'draw']);
    Route::get('/events/{eventId}/winners', [LuckyDrawController::class, 'getWinners']);

    // CERTIFICATE
    Route::post('/generate-certificate', [CertificateController::class, 'generate']);
    Route::get('/my-certificates', [CertificateController::class, 'myCertificates']);

    // SEATS
    Route::get('/events/{eventId}/seats', [SeatController::class, 'index']);
    Route::post('/book-seat', [SeatController::class, 'bookSeat']);

});