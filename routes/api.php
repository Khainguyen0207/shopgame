<?php

use App\Http\Middleware\VerifyWebMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CardDepositController;
use App\Http\Controllers\DiscountCodeController;
use Illuminate\Support\Facades\Artisan;

Route::middleware([
    VerifyWebMiddleware::class,
])->group(function () {
    Route::post('sepay-payment', [DiscountCodeController::class, 'confirmPayment']);

    Route::get('user', function (Request $request) {
        return response()->json([
            'error' => false,
            'message' => 'Success',
            'data' => [
                'user' => $request->user()
            ]
        ]);
    });
});

Route::match(['GET', 'POST'], '/callback/card', [CardDepositController::class, 'handleCallback'])->name('callback.card');

// Discount code validation
Route::post('/discount-codes/validate', [DiscountCodeController::class, 'validateCode']);

Route::get('/auto-bank-deposit', function () {
    Artisan::call('fetch:mb-transactions');
});