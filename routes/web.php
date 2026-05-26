<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ParticipantController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Admin\LuckyDrawController;
use App\Http\Controllers\Admin\CertificateController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/admin/login');
});

/*
|--------------------------------------------------------------------------
| ADMIN AUTH
|--------------------------------------------------------------------------
*/

// Login Page
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])
    ->name('admin.login');

// Login Process
Route::post('/admin/login', [AdminAuthController::class, 'login']);

// Logout
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout');

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

/*
|--------------------------------------------------------------------------
| EVENTS
|--------------------------------------------------------------------------
*/

// List Events
Route::get('/admin/events', [EventController::class, 'index'])
    ->name('admin.events');

// Create Event Page
Route::get('/admin/events/create', [EventController::class, 'create'])
    ->name('admin.events.create');

Route::post('/admin/events', [EventController::class, 'store'])
    ->name('admin.events.store');
 
// Edit Event Page
Route::get('/admin/events/{event}/edit', [EventController::class, 'edit'])
    ->name('admin.events.edit');
 
// Update Event
Route::put('/admin/events/{event}', [EventController::class, 'update'])
    ->name('admin.events.update');
 
// Delete Event
Route::delete('/admin/events/{event}', [EventController::class, 'destroy'])
    ->name('admin.events.destroy');
 
 
// Tiket
Route::get('/admin/events/{event}/tickets', [TicketCategoryController::class, 'index'])
    ->name('admin.tickets.index');
 
Route::post('/admin/events/{event}/tickets', [TicketCategoryController::class, 'store'])
    ->name('admin.tickets.store');
 
Route::delete('/admin/tickets/{ticket}', [TicketCategoryController::class, 'destroy'])
    ->name('admin.tickets.destroy');
 
/*
|--------------------------------------------------------------------------
| PARTICIPANTS
|--------------------------------------------------------------------------
*/
 
Route::get('/admin/participants', [ParticipantController::class, 'index'])
    ->name('admin.participants.index');
 
Route::post('/admin/participants/{registration}/check-in', [ParticipantController::class, 'toggleCheckIn'])
    ->name('admin.participants.check_in');
 
/*
|--------------------------------------------------------------------------
| SEATS
|--------------------------------------------------------------------------
*/
 
Route::get('/admin/seats', [SeatController::class, 'index'])
    ->name('admin.seats.index');
 
Route::post('/admin/seats/generate', [SeatController::class, 'generate'])
    ->name('admin.seats.generate');
 
Route::post('/admin/seats/{seat}/toggle-status', [SeatController::class, 'toggleStatus'])
    ->name('admin.seats.toggle_status');
 
/*
|--------------------------------------------------------------------------
| LUCKY DRAW
|--------------------------------------------------------------------------
*/
 
Route::get('/admin/lucky-draw', [LuckyDrawController::class, 'index'])
    ->name('admin.lucky_draw.index');
 
Route::post('/admin/lucky-draw/draw', [LuckyDrawController::class, 'draw'])
    ->name('admin.lucky_draw.draw');
 
Route::delete('/admin/lucky-draw/{winner}', [LuckyDrawController::class, 'destroy'])
    ->name('admin.lucky_draw.destroy');
 
/*
|--------------------------------------------------------------------------
| CERTIFICATES
|--------------------------------------------------------------------------
*/
 
Route::get('/admin/certificates', [CertificateController::class, 'index'])
    ->name('admin.certificates.index');
 
Route::post('/admin/certificates/generate', [CertificateController::class, 'generate'])
    ->name('admin.certificates.generate');
 
Route::post('/admin/certificates/{certificate}/toggle-valid', [CertificateController::class, 'toggleValid'])
    ->name('admin.certificates.toggle_valid');