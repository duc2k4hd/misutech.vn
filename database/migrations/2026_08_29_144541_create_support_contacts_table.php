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
        Schema::create('support_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('phone', 30);
            $table->string('zalo_phone', 30)->nullable();
            $table->string('department', 100)->default('Phòng Kinh Doanh / Báo Giá');
            $table->enum('department_type', ['sale', 'technical', 'warranty', 'other'])->default('sale');
            $table->string('avatar', 255)->nullable();
            $table->string('note', 255)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_popup')->default(true);
            $table->timestamps();

            $table->index('department_type');
            $table->index('sort_order');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_contacts');
    }
};
