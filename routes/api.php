<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Event\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth
Route::post('register', [AuthController::class, 'register']);
Route::post('verify-otp', [OtpController::class, 'verifyOtp']);
Route::post('resend-otp', [OtpController::class, 'resendOtp']);
// ->middleware('guest');
Route::post('login', [AuthController::class, 'login']);
// Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', fn (Request $request) => $request->user());
});


// Category
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
// Event
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Bookings (Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events/{event}/book', [BookingController::class, 'book']);
    Route::get('/my-bookings', [BookingController::class, 'myBooking']);
    Route::delete('/my-bookings/{booking}', [BookingController::class, 'cancel']);
});



/*
|--------------------------------------------------------------------------
| Admin Routes (Policy Protected)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Categories
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Events
    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::delete('/events/{event}', [EventController::class, 'destroy']);
});