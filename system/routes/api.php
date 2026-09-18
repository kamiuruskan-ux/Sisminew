<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

Route::post('payment/midtrans/callback', [PaymentController::class, 'midtransCallback'])->name('api.payment.midtrans.callback');
Route::post('payment/tripay/callback', [PaymentController::class, 'tripayCallback'])->name('api.payment.tripay.callback');
Route::post('payment/duitku/callback', [PaymentController::class, 'duitkuCallback'])->name('api.payment.duitku.callback');

