<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function getOrders()
    {
        // Fetch all orders, newest first, and include the customer details and items
        $orders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($orders);
    }

    public function storeProduct(Request $request)
{
    // 1. Validate the incoming data (including the image file)
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|integer', // Remember: Lowest denomination!
        'stock_quantity' => 'required|integer',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB image
    ]);

    $imagePath = null;

    // 2. Check if an image was actually uploaded
    if ($request->hasFile('image')) {
        // Store it in the 'public/products' folder and get the file path
        $path = $request->file('image')->store('products', 'public');
        
        // Convert the path into a full URL (e.g., http://localhost:8000/storage/products/image.jpg)
        $imagePath = asset('storage/' . $path);
    }

    // 3. Create the product in the database
    $product = Product::create([
        'name' => $validated['name'],
        'slug' => Str::slug($validated['name']) . '-' . Str::random(5), // Unique URL slug
        'description' => $validated['description'],
        'price' => $validated['price'],
        'stock_quantity' => $validated['stock_quantity'],
        'sku' => 'PRD-' . strtoupper(Str::random(6)),
        'image_url' => $imagePath,
        'is_active' => true,
    ]);

    return response()->json([
        'message' => 'Product created successfully!',
        'product' => $product
    ], 201);
}
}