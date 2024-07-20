<?php

use App\Http\Controllers\BorrowedRoomController;
use App\Http\Controllers\FloorController;
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

Route::apiResource('room', RoomController::class)->only('index', 'show');
Route::apiResource('floor', FloorController::class)->only('index', 'show');
Route::apiResource('item', ItemController::class)->only('index', 'show');

Route::apiResource('borrowed-room', BorrowedRoomController::class)->only('index', 'store', 'show');