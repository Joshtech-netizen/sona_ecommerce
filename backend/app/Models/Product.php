<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // 1. Allow these columns to be mass-assigned when creating/updating
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock_quantity',
        'sku',
        'attributes',
        'is_active',
        'image_url', // <-- The new image column is now protected and ready!
    ];

    // 2. Automatically cast data types when pulling from the PostgreSQL database
    protected $casts = [
        'price' => 'integer',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
        'attributes' => 'array', // Automatically converts your JSONB column into a usable PHP array
    ];
}