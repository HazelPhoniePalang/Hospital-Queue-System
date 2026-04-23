<?php

use App\Http\Controllers\Api\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('payments')->group(function () {
    Route::post('/cash', [PaymentController::class, 'processCashPayment']);
    Route::post('/gcash', [PaymentController::class, 'processGcashPayment']);
    Route::post('/card', [PaymentController::class, 'processCardPayment']);
    Route::get('/{paymentId}', [PaymentController::class, 'show']);
    Route::get('/{paymentId}/receipt', [PaymentController::class, 'receipt']);
});
