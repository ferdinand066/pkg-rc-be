<?php

use App\Http\Controllers\Auth\CurrentUserController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\BorrowedRoomController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoomController;
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

Route::apiResource('room', RoomController::class)->only('index', 'show');
Route::apiResource('floor', FloorController::class)->only('index', 'show');
Route::apiResource('item', ItemController::class)->only('index', 'show');

Route::apiResource('borrowed-room', BorrowedRoomController::class)->only('index', 'show');
Route::middleware(['verified'])->group(function(){
    Route::apiResource('borrowed-room', BorrowedRoomController::class)->only('store', 'update', 'destroy');
});
