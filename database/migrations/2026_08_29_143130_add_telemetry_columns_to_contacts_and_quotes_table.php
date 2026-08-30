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
        // 1. Thêm telemetry vào bảng contacts
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('status');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('referer', 255)->nullable()->after('user_agent');
            $table->string('device_type', 30)->nullable()->after('referer');
            $table->unsignedInteger('duration_seconds')->default(0)->after('device_type');
            $table->json('meta_data')->nullable()->after('duration_seconds');
        });

        // 2. Thêm telemetry vào bảng quotes
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('referer', 255)->nullable()->after('user_agent');
            $table->string('device_type', 30)->nullable()->after('referer');
            $table->json('meta_data')->nullable()->after('duration_seconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_agent', 'referer', 'device_type', 'duration_seconds', 'meta_data']);
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn(['referer', 'device_type', 'meta_data']);
        });
    }
};
