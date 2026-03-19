<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Allow these exact columns to be mass-assigned
    protected $fillable = [
        'user_id', 
        'reference', 
        'total_amount', 
        'status'
    ];

    // Optional but good practice: define the relationship back to the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship to the order items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}