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
        DB::statement("ALTER TABLE product_media MODIFY COLUMN role ENUM('thumbnail', 'gallery', 'catalog') DEFAULT 'gallery'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE product_media MODIFY COLUMN role ENUM('thumbnail', 'gallery') DEFAULT 'gallery'");
    }
};
