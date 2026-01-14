<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\JobOfferController as ApiJobOfferController;
use App\Http\Controllers\api\v1\TestController as ApiTestController;
use App\Http\Controllers\api\v1\TestAttemptController;
use App\Http\Controllers\api\v1\TechnologyController as ApiTechnologyController;

Route::prefix('v1')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('api.register');
        Route::post('/verify-code', 'verify')->name('api.verify');
        Route::post('/resend-code', 'resend')->name('api.resend-code');
        Route::post('/login', 'login')->name('api.login');
        Route::post('/logout', 'logout')->name('api.logout')->middleware('jwt.auth');
        Route::post('/refresh-token', 'refreshToken')->name('api.refresh-token')->middleware('jwt.auth');
        Route::post('/forgot-password', 'sendPasswordResetCode')->name('api.send-password-reset-code');
        Route::post('/reset-password', 'resetPasswordWithCode')->name('api.reset-password-with-code');
        Route::get('/me', 'me')->name('api.me')->middleware('jwt.auth');
    });


    Route::middleware('jwt.auth')->group(function () {

    });

});
