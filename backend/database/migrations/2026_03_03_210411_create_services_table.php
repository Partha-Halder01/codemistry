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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->integer('full_price');
            $table->integer('deposit_price');
            $table->text('features');
            $table->string('cover_image_path')->nullable();
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->json('faq')->nullable()->comment('JSON array of Q&A pairs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
