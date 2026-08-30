<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên dòng sản phẩm (VD: Omron E3Z-D)');
            $table->string('slug')->unique()->comment('Đường dẫn SEO');
            $table->text('description')->nullable()->comment('Mô tả ngắn về dòng sản phẩm');
            $table->longText('content')->nullable()->comment('Nội dung giới thiệu chi tiết dòng sản phẩm');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete()->comment('Thương hiệu');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->comment('Danh mục chính');
            $table->integer('sort_order')->default(0)->index()->comment('Thứ tự hiển thị');
            $table->enum('status', ['active', 'draft'])->default('active')->index()->comment('Trạng thái');
            $table->string('meta_title')->nullable()->comment('Tiêu đề SEO');
            $table->string('meta_description')->nullable()->comment('Mô tả SEO');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('series');
    }
};
