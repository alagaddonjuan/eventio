<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RealtimeAuthController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
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

    // Ticket Routes
    Route::get('/events/{event}/tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::post('/events/{event}/tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::delete('/events/{event}/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Master Admin Routes
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/toggle-premium', [\App\Http\Controllers\AdminController::class, 'togglePremium'])->name('toggle-premium');
});

// Guest portal (no login required)
Route::get('/rsvp/{event_token}', [GuestController::class, 'rsvp'])->name('guest.rsvp');
Route::post('/rsvp/{event_token}', [GuestController::class, 'storeGuest'])->name('guest.store');
Route::get('/portal/{token}', [GuestController::class, 'portal'])->name('guest.portal');

Route::post('/api/guest/{token}/check-in', [GuestController::class, 'checkIn'])->name('guest.checkin');

// Photo Gallery API routes
Route::get('/api/events/{token}/gallery', [\App\Http\Controllers\EventMediaController::class, 'index']);
Route::post('/api/events/{token}/gallery', [\App\Http\Controllers\EventMediaController::class, 'store']);

require __DIR__.'/auth.php';
