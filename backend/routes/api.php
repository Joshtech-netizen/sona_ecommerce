<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\PaystackWebhookController; 
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;

// Protected routes: Only logged-in users with a valid Sanctum token can access these
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'initializeCheckout']);
    });

// Admin routes: Only users with the 'admin' role can access these
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/admin/orders', [AdminController::class, 'getOrders']);
    Route::post('/admin/products', [AdminController::class, 'storeProduct']);
});

// Public Webhook Route: Paystack's servers need to reach this without logging in
Route::post('/webhooks/paystack', [PaystackWebhookController::class, 'handleGatewayCallback']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);


