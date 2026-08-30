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
        Schema::table('products', function (Blueprint $table) {
            // Composite index giúp truy vấn theo series_id + status + created_at chạy trong ~0.001s trên hàng triệu dòng
            $table->index(['series_id', 'status', 'created_at'], 'idx_products_series_status_created');
        });

        Schema::table('series', function (Blueprint $table) {
            // Composite index cho series theo status + created_at
            $table->index(['status', 'created_at'], 'idx_series_status_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_series_status_created');
        });

        Schema::table('series', function (Blueprint $table) {
            $table->dropIndex('idx_series_status_created');
        });
    }
};
