<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BorrowedRoomController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoomController;
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

Route::post('user/{user}/activate', [UserController::class, 'activate'])->name('user.activate');
Route::apiResource('user', UserController::class);
Route::apiResource('room', RoomController::class)->only('store', 'update', 'destroy');
Route::apiResource('item', ItemController::class)->only('store', 'update', 'destroy');

Route::prefix('borrowed-room')->name('borrowed-room.')->group(function(){
    Route::post('{borrowed_room}/accept', [BorrowedRoomController::class, 'accept'])->name('accept');
    Route::post('{borrowed_room}/decline', [BorrowedRoomController::class, 'decline'])->name('decline');
});
