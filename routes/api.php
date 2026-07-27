<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/mpesa/callback', [PaymentController::class, 'handleCallback'])->name('api.mpesa.callback');
