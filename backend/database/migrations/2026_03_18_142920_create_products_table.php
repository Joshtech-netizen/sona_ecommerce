<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique(); // For clean SEO URLs (e.g., /products/red-sneakers)
        $table->text('description')->nullable();
        
        // SECURITY: Store price as an integer (e.g., 1000 = 10.00)
        $table->integer('price'); 
        
        // Inventory tracking
        $table->integer('stock_quantity')->default(0); 
        $table->string('sku')->unique(); // Stock Keeping Unit
        
        // POSTGRESQL SUPERPOWER: Flexible attributes
        // This lets you store { "color": "red", "size": "XL", "material": "cotton" }
        // without needing a complex web of extra tables.
        $table->jsonb('attributes')->nullable(); 
        
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
