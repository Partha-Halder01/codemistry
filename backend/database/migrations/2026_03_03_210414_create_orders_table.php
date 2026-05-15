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
            $table->string('order_uid', 50)->nullable()->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->enum('payment_type', ['full', 'deposit']);
            $table->integer('total_service_price')->comment('The full price in paise');
            $table->integer('amount_paid')->comment('Amount paid in paise');
            $table->string('razorpay_payment_id');
            $table->string('coupon_code', 50)->nullable();
            $table->string('status')->default('Paid');
            $table->timestamp('order_date')->useCurrent();
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
