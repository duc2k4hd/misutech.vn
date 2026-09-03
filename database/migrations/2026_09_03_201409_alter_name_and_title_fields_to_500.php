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
        $nameTables = ['products', 'categories', 'brands', 'series'];
        foreach ($nameTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'name')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('name', 500)->change();
                });
            }
        }

        if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'title')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('title', 500)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $nameTables = ['products', 'categories', 'brands', 'series'];
        foreach ($nameTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'name')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('name', 191)->change();
                });
            }
        }

        if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'title')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('title', 191)->change();
            });
        }
    }
};
