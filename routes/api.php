<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Event\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth
Route::post('register', [AuthController::class, 'register'])->middleware('guest');
Route::post('verify-otp', [OtpController::class, 'verifyOtp'])->middleware('guest');
Route::post('resend-otp', [OtpController::class, 'resendOtp'])->middleware('guest');
Route::post('login', [AuthController::class, 'login'])->middleware('guest');
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('category.index', [CategoryController::class, 'index']);

// Category
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Category
    Route::post('category.store', [CategoryController::class, 'store'])->middleware('role:admin');
    Route::post('category.update/{id}', [CategoryController::class, 'update'])->middleware('role:admin');
    Route::delete('category.delete/{id}', [CategoryController::class, 'destroy'])->middleware('role:admin');


    // Event
    Route::post('event.store', [EventController::class, 'store']);
    Route::put('event/{id}', [EventController::class, 'update']);
    Route::delete('event/{id}', [EventController::class, 'destroy']);
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('event/booking/{eventId}', [BookingController::class, 'book']);
    Route::get('mybooking', [BookingController::class, 'myBooking']);
    Route::post('cancel/{id}', [BookingController::class, 'cancel']);
});
// Category
Route::get('category.index', [CategoryController::class, 'index']);
Route::get('category.show/{id}', [CategoryController::class, 'show']);

// Event
Route::get('event.index', [EventController::class, 'index']);
Route::get('event.show/{id}', [EventController::class, 'show']);
