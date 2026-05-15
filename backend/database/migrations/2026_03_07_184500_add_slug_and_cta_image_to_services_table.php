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
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('cta_image_path')->nullable()->after('cover_image_path');
            $table->json('process_steps')->nullable()->after('faq')->comment('JSON array of process steps with title, description, and icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['slug', 'cta_image_path', 'process_steps']);
        });
    }
};
