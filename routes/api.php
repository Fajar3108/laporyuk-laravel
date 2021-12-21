<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintSupportController;
use App\Http\Controllers\ResponseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::resource('complaints', ComplaintController::class)->except(['edit', 'create']);
    Route::middleware(['is_not_masyarakat'])->group(function () {
        Route::resource('responses', ResponseController::class)->only('store', 'destroy');
    });
    Route::middleware(['is_masyarakat'])->group(function () {
        Route::resource('supports', ComplaintSupportController::class)->only('store', 'destroy');
    });
    Route::middleware(['is_admin'])->group(function () {
        Route::resource('user', UserController::class)->only('store', 'destroy');
    });
});
