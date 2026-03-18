<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function handleGatewayCallback(Request $request)
    {
        // 1. Verify the signature to ensure the request is actually from Paystack
        $secret = env('PAYSTACK_SECRET_KEY');
        $signature = $request->header('x-paystack-signature');

        // Paystack creates a hash of the payload using your secret key. We do the same to compare.
        $computedSignature = hash_hmac('sha512', $request->getContent(), $secret);

        if ($signature !== $computedSignature) {
            // Log the attempt for your security audit
            Log::warning('Unauthorized Paystack Webhook Attempt', ['ip' => $request->ip()]);
            abort(401, 'Invalid Signature');
        }

        // 2. The signature is valid! Decode the payload.
        $payload = json_decode($request->getContent(), true);
        $event = $payload['event'];

        // 3. Handle the successful payment event
        if ($event === 'charge.success') {
            $orderReference = $payload['data']['reference'];
            
            // Find the pending order in our database
            $order = Order::where('reference', $orderReference)->first();

            if ($order && $order->status === 'pending') {
                // Update the order status to paid
                $order->update(['status' => 'paid']);
                
                Log::info("Order {$orderReference} marked as paid.");
                
                // TODO later: Trigger an event here to send a receipt email or notify the shipping department!
            }
        }

        // 4. Always return a 200 OK fast so Paystack knows you received it and stops retrying
        return response()->json(['status' => 'success'], 200);
    }
}