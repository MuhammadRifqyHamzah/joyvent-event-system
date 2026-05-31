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
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SettingsController;

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
| ADMIN AUTH (GUEST ONLY)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login Page
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])
        ->name('admin.login');

    // Login Process
    Route::post('/admin/login', [AdminAuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL (PROTECTED)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

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

    // Dedicated Event Detail Routes based on status
    Route::get('/admin/events/{event}/upcoming', [EventController::class, 'showUpcoming'])
        ->name('admin.events.upcoming');

    Route::get('/admin/events/{event}/ongoing', [EventController::class, 'showOngoing'])
        ->name('admin.events.ongoing');

    Route::get('/admin/events/{event}/finished', [EventController::class, 'showFinished'])
        ->name('admin.events.finished');

    // Realtime stats API for On-Going Event
    Route::get('/admin/events/{event}/ongoing/stats', [EventController::class, 'ongoingStats'])
        ->name('admin.events.ongoing.stats');

    // QR Code check-in endpoint for On-Going Event
    Route::post('/admin/events/{event}/check-in-qr', [EventController::class, 'checkInQr'])
        ->name('admin.events.check_in_qr');

    // Show Event Details (Fallback router)
    Route::get('/admin/events/{event}', [EventController::class, 'show'])
        ->name('admin.events.show');

    // Event Feature Setup Pages
    Route::get('/admin/events/{event}/features', [EventController::class, 'showFeatures'])
        ->name('admin.events.features');

    Route::post('/admin/events/{event}/features', [EventController::class, 'storeFeatures'])
        ->name('admin.events.features.store');

    Route::get('/admin/events/{event}/finish', [EventController::class, 'finishSetup'])
        ->name('admin.events.finish');
     
     
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
     
    Route::get('/admin/events/{event}/participants', [ParticipantController::class, 'index'])
        ->name('admin.participants.index');
     
    Route::post('/admin/participants/{registration}/check-in', [ParticipantController::class, 'toggleCheckIn'])
        ->name('admin.participants.check_in');
     
    Route::post('/admin/events/{event}/participants/reset', [ParticipantController::class, 'resetCheckIn'])
        ->name('admin.participants.reset_check_in');

    /*
    |--------------------------------------------------------------------------
    | REFUNDS
    |--------------------------------------------------------------------------
    */
    Route::post('/admin/refunds/{refund}/approve', [\App\Http\Controllers\Admin\RefundController::class, 'approve'])
        ->name('admin.refunds.approve');
    Route::post('/admin/refunds/{refund}/reject', [\App\Http\Controllers\Admin\RefundController::class, 'reject'])
        ->name('admin.refunds.reject');
     
    /*
    |--------------------------------------------------------------------------
    | SEATS
    |--------------------------------------------------------------------------
    */
     
    Route::get('/admin/events/{event}/seats', [SeatController::class, 'index'])
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
     
    Route::get('/admin/events/{event}/lucky-draw', [LuckyDrawController::class, 'index'])
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
     
    Route::get('/admin/events/{event}/certificates', [CertificateController::class, 'index'])
        ->name('admin.certificates.index');
     
    Route::post('/admin/certificates/generate', [CertificateController::class, 'generate'])
        ->name('admin.certificates.generate');
     
    Route::post('/admin/certificates/{certificate}/toggle-valid', [CertificateController::class, 'toggleValid'])
        ->name('admin.certificates.toggle_valid');

    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/reports', [ReportController::class, 'index'])
        ->name('admin.reports');

    Route::get('/admin/reports/data/{event}', [ReportController::class, 'getData'])
        ->name('admin.reports.data');

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/notifications', [NotificationController::class, 'index'])
        ->name('admin.notifications');

    Route::get('/admin/notifications/{notification}/click', [NotificationController::class, 'handleClick'])
        ->name('admin.notifications.click');

    Route::post('/admin/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('admin.notifications.mark_read');

    Route::post('/admin/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
        ->name('admin.notifications.mark_all_read');

    Route::delete('/admin/notifications/{notification}', [NotificationController::class, 'destroy'])
        ->name('admin.notifications.destroy');

    /*
    |--------------------------------------------------------------------------
    | SETTINGS
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/settings', [SettingsController::class, 'index'])
        ->name('admin.settings');
    Route::post('/admin/settings/profile', [SettingsController::class, 'updateProfile'])
        ->name('admin.settings.profile');
    Route::post('/admin/settings/password', [SettingsController::class, 'updatePassword'])
        ->name('admin.settings.password');
    Route::post('/admin/settings/organizer', [SettingsController::class, 'updateOrganizer'])
        ->name('admin.settings.organizer');
});