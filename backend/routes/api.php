<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\PaystackWebhookController; // <-- Added the semicolon here!

// Protected routes: Only logged-in users with a valid Sanctum token can access these
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'initializeCheckout']);
});

// Public Webhook Route: Paystack's servers need to reach this without logging in
Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handleGatewayCallback']);
