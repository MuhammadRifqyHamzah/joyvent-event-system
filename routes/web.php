<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\TicketCategoryController;

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

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

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


// Tiket
Route::get('/admin/events/{event}/tickets', [TicketCategoryController::class, 'index'])
    ->name('admin.tickets.index');