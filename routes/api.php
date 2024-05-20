<?php

use App\Http\Controllers\Auth\CurrentUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('login', LoginController::class)->name('login');
    // Route::post('login/social-media', SocialMediaLoginController::class)->name('login.social-media');
    Route::post('register', RegisterController::class)->name('register');

    Route::post('forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset-password');
    Route::get('token/{token}', [ForgotPasswordController::class, 'checkTokenValidity'])->name('token.valid');

    Route::get('me', CurrentUserController::class)->name('me')->middleware('auth:api');
});

Route::prefix('email')->name('verification.')->group(function () {
    Route::get('verify/{id}', [VerificationController::class, 'verify'])->name('verify');
    Route::post('resend', [VerificationController::class, 'resend'])->middleware('auth:api')->name('resend');
});
