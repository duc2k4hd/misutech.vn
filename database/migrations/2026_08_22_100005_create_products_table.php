<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên sản phẩm');
            $table->string('slug')->unique()->comment('Đường dẫn tĩnh chuẩn SEO');
            $table->string('sku')->unique()->comment('Mã sản phẩm (SKU)');
            $table->decimal('price', 15, 2)->index()->comment('Giá bán thường');
            $table->decimal('sale_price', 15, 2)->nullable()->index()->comment('Giá khuyến mãi');
            $table->text('short_description')->nullable()->comment('Mô tả ngắn');
            $table->longText('content')->nullable()->comment('Nội dung chi tiết');
            $table->string('thumbnail')->nullable()->comment('Ảnh đại diện chính');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete()->comment('Danh mục chính');
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete()->comment('Thương hiệu');
            
            // Cache columns for performance
            $table->unsignedBigInteger('views_count')->default(0)->index()->comment('Lượt xem (Cache)');
            $table->decimal('rating_average', 3, 2)->default(0.00)->index()->comment('Đánh giá trung bình (Cache)');
            $table->unsignedInteger('reviews_count')->default(0)->comment('Số lượng đánh giá (Cache)');
            
            // SEO
            $table->string('meta_title')->nullable()->comment('Tiêu đề SEO');
            $table->string('meta_description')->nullable()->comment('Mô tả SEO');
            
            $table->enum('status', ['active', 'draft'])->default('active')->index()->comment('Trạng thái hiển thị');
            $table->timestamp('published_at')->nullable()->index()->comment('Ngày giờ xuất bản');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down() { Schema::dropIfExists('products'); }
};