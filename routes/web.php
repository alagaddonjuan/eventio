<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RealtimeAuthController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    $upcomingEvents = \App\Models\Event::with('tickets')
        ->where('event_date', '>=', now())
        ->orderBy('event_date', 'asc')
        ->take(6) // Show top 6 upcoming events
        ->get();
        
    return view('welcome', compact('upcomingEvents'));
})->name('home');

Route::get('/dashboard', [EventController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Legal Routes
Route::get('/terms', function () {
    return view('legal.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('legal.privacy');
})->name('privacy');

// Google Socialite Routes
Route::get('/auth/google', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'handleGoogleCallback']);

// Node.js authentication endpoint (needs CSRF exception)
Route::post('/api/realtime/verify', [RealtimeAuthController::class, 'verify']);

// Host dashboard routes (requires login)
Route::middleware('auth')->group(function () {
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::get('/events/{event}/command-center', [EventController::class, 'commandCenter'])->name('events.command-center');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Livestream Routes
    Route::post('/events/{event}/start-stream', [EventController::class, 'startStream'])->name('events.start-stream');
    Route::post('/events/{event}/end-stream', [EventController::class, 'endStream'])->name('events.end-stream');
    Route::post('/events/{event}/blast', [EventController::class, 'sendBlast'])->name('events.blast');

    // Ticket Routes
    Route::get('/events/{event}/tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::post('/events/{event}/tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::delete('/events/{event}/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.destroy');

    // Promo Code Routes
    Route::post('/events/{event}/promo-codes', [\App\Http\Controllers\PromoCodeController::class, 'store'])->name('promo-codes.store');
    Route::delete('/events/{event}/promo-codes/{promoCode}', [\App\Http\Controllers\PromoCodeController::class, 'destroy'])->name('promo-codes.destroy');

    // Support Ticket Routes (Host)
    Route::get('/events/{event}/support', [\App\Http\Controllers\SupportController::class, 'hostIndex'])->name('support.index');
    Route::post('/events/support/{ticketId}/reply', [\App\Http\Controllers\SupportController::class, 'storeMessage'])->name('support.reply');
    Route::post('/events/support/{ticketId}/close', [\App\Http\Controllers\SupportController::class, 'closeTicket'])->name('support.close');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/bank', [ProfileController::class, 'updateBank'])->name('profile.bank');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Master Admin Routes
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/toggle-premium', [\App\Http\Controllers\AdminController::class, 'togglePremium'])->name('toggle-premium');
    
    // User management
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::delete('/users/{user}', [\App\Http\Controllers\AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Event management
    Route::get('/events', [\App\Http\Controllers\AdminController::class, 'events'])->name('events');
    Route::delete('/events/{event}', [\App\Http\Controllers\AdminController::class, 'destroyEvent'])->name('events.destroy');
});

// Guest portal (no login required)
Route::get('/rsvp/{event_token}', [GuestController::class, 'rsvp'])->name('guest.rsvp');
Route::post('/rsvp/{event_token}', [GuestController::class, 'storeGuest'])->name('guest.store');
Route::get('/portal/{token}', [GuestController::class, 'portal'])->name('guest.portal');

// Guest Support Routes
Route::get('/portal/{token}/support', [\App\Http\Controllers\SupportController::class, 'guestIndex'])->name('guest.support');
Route::post('/portal/{token}/support', [\App\Http\Controllers\SupportController::class, 'guestStoreTicket'])->name('guest.support.store');
Route::post('/portal/support/{ticketId}/reply', [\App\Http\Controllers\SupportController::class, 'storeMessage'])->name('guest.support.reply');

// Guest ticket payment flow
Route::get('/guest/payment', [\App\Http\Controllers\PaymentController::class, 'showPaymentForm'])->name('guest.payment.form');
Route::post('/guest/payment/initiate', [\App\Http\Controllers\PaymentController::class, 'initiateCharge'])->name('guest.payment.initiate');
Route::get('/guest/payment/status', [\App\Http\Controllers\PaymentController::class, 'checkStatus'])->name('guest.payment.status');
Route::get('/guest/payment/callback', [\App\Http\Controllers\PaymentController::class, 'handleCallback'])->name('guest.payment.callback');

Route::post('/api/guest/{token}/check-in', [GuestController::class, 'checkIn'])->name('guest.checkin');

// Photo Gallery API routes
Route::get('/api/events/{token}/gallery', [\App\Http\Controllers\EventMediaController::class, 'index']);
Route::post('/api/events/{token}/gallery', [\App\Http\Controllers\EventMediaController::class, 'store']);

require __DIR__.'/auth.php';
Route::get('/verify/{token}', [GuestController::class, 'verifyTicket'])->name('guest.verify');
