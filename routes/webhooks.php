<?php

use App\Http\Controllers\Webhooks\TelegramSelfServiceController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth.api_key'], 'prefix' => 'webhooks'], function () {
    Route::post('telegram', TelegramSelfServiceController::class)
        ->name('webhook.telegram');
});