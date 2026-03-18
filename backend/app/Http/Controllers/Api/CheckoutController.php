<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function initializeCheckout(Request $request)
    {
        // 1. Validate what the frontend sends. Notice we DO NOT ask for a price.
        // We only expect an array of product IDs and quantities.
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // We use a database transaction. If anything fails (like a product is out of stock),
        // the entire process rolls back automatically. No half-finished orders.
        return DB::transaction(function () use ($validated, $request) {
            
            $totalAmount = 0;
            $orderItemsData = [];

            // 2. Loop through the items to calculate the real total securely
            foreach ($validated['items'] as $item) {
                // Fetch the product directly from our secure database
                $product = Product::lockForUpdate()->find($item['product_id']);

                // Verify stock
                if ($product->stock_quantity < $item['quantity']) {
                    return response()->json([
                        'message' => "Sorry, {$product->name} does not have enough stock."
                    ], 400);
                }

                // Calculate the true cost for this line item
                $lineTotal = $product->price * $item['quantity'];
                $totalAmount += $lineTotal;

                // Prepare the order item record
                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price, 
                ];
            }

            // 3. Create the Pending Order
            $order = Order::create([
                'user_id' => $request->user()->id, // The authenticated user from Sanctum
                'reference' => 'ORD-' . Str::random(15), // Unique reference for Paystack
                'total_amount' => $totalAmount,
                'status' => 'pending',
            ]);

            // 4. Save the Order Items
            foreach ($orderItemsData as $itemData) {
                $order->items()->create($itemData); // Assuming you add the items() relationship to the Order model
            }

            // 5. Return the calculated data to the frontend so it can initialize Paystack
            return response()->json([
                'message' => 'Checkout initialized successfully',
                'order_reference' => $order->reference,
                'total_amount' => $totalAmount, // This is what the frontend will send to the Paystack popup
            ], 200);
        });
    }
}