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
        $tables = ['products', 'posts', 'brands', 'series'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'meta_description')) {
                        $table->text('meta_description')->nullable()->change();
                    }
                    if (Schema::hasColumn($tableName, 'meta_title')) {
                        $table->string('meta_title', 500)->nullable()->change();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['products', 'posts', 'brands', 'series'];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'meta_description')) {
                        $table->string('meta_description', 191)->nullable()->change();
                    }
                    if (Schema::hasColumn($tableName, 'meta_title')) {
                        $table->string('meta_title', 191)->nullable()->change();
                    }
                });
            }
        }
    }
};
