<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Tên cấu hình (VD: logo, hotline)');
            $table->text('value')->nullable()->comment('Giá trị cấu hình');
            $table->string('type')->default('text')->comment('Loại dữ liệu (text, image, json)');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('settings'); }
};