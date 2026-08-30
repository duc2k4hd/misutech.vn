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
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('author_name', 100)->nullable()->after('user_id');
            $table->string('author_phone', 20)->nullable()->after('author_name');
            $table->string('author_email', 100)->nullable()->after('author_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'author_phone', 'author_email']);
        });
    }
};
