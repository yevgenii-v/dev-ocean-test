<?php

use App\Http\Controllers\Authentication\AuthenticationController;
use App\Http\Middleware\GuestMiddleware;
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

Route::get('swagger', function () {
    return response()->file(public_path() . '/swagger.json');
});

Route::group(['prefix' => 'v1'], function () {
    Route::name('auth.')->group(function () {

        Route::middleware(GuestMiddleware::class)->group(function () {
            Route::post('/register', [AuthenticationController::class, 'register'])->name('register');
            Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
        });

        Route::middleware('auth:api')->group(function () {
            Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
            Route::get('/profile', [AuthenticationController::class, 'profile'])->name('profile');
        });
    });

});
