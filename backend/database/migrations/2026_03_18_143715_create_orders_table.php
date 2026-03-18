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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // A unique string we will send to Paystack to track this specific payment
        $table->string('reference')->unique(); 
        
        // Total calculated securely on the backend (in lowest denomination, e.g., pesewas/cents)
        $table->integer('total_amount'); 
        
        // payment status: 'pending', 'paid', 'failed'
        $table->string('status')->default('pending'); 
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
