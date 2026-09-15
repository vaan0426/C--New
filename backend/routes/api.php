<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventTicketController;
use App\Http\Controllers\Api\HelpTypeController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\PackagePurchaseController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SlotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes — no auth required (frontend browsing + guest booking)
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');

Route::get('/help-types', [HelpTypeController::class, 'index']);
Route::get('/packages', [PackageController::class, 'index']);
Route::get('/slots', [SlotController::class, 'index']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event:slug}', [EventController::class, 'show']);
Route::get('/pages/{slug}', [PageController::class, 'show']);

// Guest booking (spec §4.1: creates the account inline)
Route::post('/bookings', [BookingController::class, 'store']);

Route::post('/stripe/webhook', [PaymentController::class, 'webhook']);

/*
|--------------------------------------------------------------------------
| Authenticated frontend routes — any logged-in role
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);

    Route::get('/my-bookings', [BookingController::class, 'index']);

    Route::get('/my-packages', [PackagePurchaseController::class, 'index']);
    Route::post('/packages/checkout', [PackagePurchaseController::class, 'store']);

    Route::get('/my-tickets', [EventTicketController::class, 'index']);
    Route::post('/event-tickets', [EventTicketController::class, 'store']);
    Route::delete('/event-tickets/{eventTicket}', [EventTicketController::class, 'destroy']);

    Route::post('/payments/{payment}/checkout-session', [PaymentController::class, 'createCheckoutSession']);

    /*
    |----------------------------------------------------------------------
    | Admin / editor routes — role-gated (spec §2.2, §6)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,editor')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::get('/bookings', [BookingController::class, 'adminIndex']);
        Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm']);
        Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject']);
        Route::post('/bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
        Route::put('/bookings/{booking}/internal-note', [BookingController::class, 'updateInternalNote']);

        Route::get('/slots', [SlotController::class, 'adminIndex']);
        Route::post('/slots', [SlotController::class, 'store']);
        Route::delete('/slots/{slot}', [SlotController::class, 'destroy']);

        Route::get('/help-types', [HelpTypeController::class, 'adminIndex']);
        Route::post('/help-types', [HelpTypeController::class, 'store']);
        Route::put('/help-types/{helpType}', [HelpTypeController::class, 'update']);
        Route::delete('/help-types/{helpType}', [HelpTypeController::class, 'destroy']);

        Route::get('/packages', [PackageController::class, 'adminIndex']);
        Route::post('/packages', [PackageController::class, 'store']);
        Route::put('/packages/{package}', [PackageController::class, 'update']);
        Route::delete('/packages/{package}', [PackageController::class, 'destroy']);

        Route::get('/events', [EventController::class, 'adminIndex']);
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);

        Route::get('/pages', [PageController::class, 'adminIndex']);
        Route::post('/pages', [PageController::class, 'store']);
        Route::put('/pages/{page}', [PageController::class, 'update']);
        Route::delete('/pages/{page}', [PageController::class, 'destroy']);

        Route::post('/payments/{payment}/mark-paid', [PaymentController::class, 'markPaid']);

        // Admin-only user management (editor cannot manage users/roles)
        Route::middleware('role:admin')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/users/{user}', [UserController::class, 'show']);
            Route::put('/users/{user}', [UserController::class, 'update']);
        });
    });
});
