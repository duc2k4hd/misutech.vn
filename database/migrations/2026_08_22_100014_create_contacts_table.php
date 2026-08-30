<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên người liên hệ');
            $table->string('email')->comment('Email');
            $table->string('phone')->nullable()->comment('SĐT');
            $table->string('subject')->nullable()->comment('Tiêu đề');
            $table->text('message')->comment('Nội dung');
            $table->enum('status', ['pending', 'resolved'])->default('pending')->index();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('contacts'); }
};