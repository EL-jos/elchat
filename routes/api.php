<?php

use App\Http\Controllers\api\v1\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\SiteController;
use App\Http\Controllers\api\v1\AuthController;
use Illuminate\Support\Facades\Log;

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
        Route::apiResource('site', SiteController::class);
        Route::controller(SiteController::class)->group(function () {
            Route::post('site/{id}/crawl', 'crawl');
        });
        Route::post('/chat/ask', [ChatController::class, 'ask']);
    });


    Route::get('/test-log', function () {
        Log::debug('✅ Test de log DEBUG');
        Log::info('✅ Test de log INFO');
        return 'Logs envoyés ! Vérifie storage/logs/laravel.log';
    });
});

