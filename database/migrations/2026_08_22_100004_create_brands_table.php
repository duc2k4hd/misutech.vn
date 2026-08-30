<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên thương hiệu');
            $table->string('slug')->unique()->comment('Đường dẫn tĩnh');
            $table->string('logo')->nullable()->comment('Logo thương hiệu');
            $table->longText('content')->nullable()->comment('Nội dung mô tả thương hiệu');
            
            // SEO
            $table->string('meta_title')->nullable()->comment('Tiêu đề SEO');
            $table->string('meta_description')->nullable()->comment('Mô tả SEO');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('brands'); }
};