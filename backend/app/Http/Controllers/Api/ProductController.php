<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Fetch all active products. You can paginate this later!
        $products = Product::where('is_active', true)->get();
        
        return response()->json($products);
    }
}