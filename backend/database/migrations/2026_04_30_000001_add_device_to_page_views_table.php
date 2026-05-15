<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->string('user_agent', 512)->nullable()->after('ip_address');
            $table->string('device_type', 20)->nullable()->after('user_agent')->index();
            $table->string('browser', 50)->nullable()->after('device_type');
            $table->string('referrer', 512)->nullable()->after('browser');
        });
    }

    public function down(): void
    {
        Schema::table('page_views', function (Blueprint $table) {
            $table->dropColumn(['user_agent', 'device_type', 'browser', 'referrer']);
        });
    }
};
