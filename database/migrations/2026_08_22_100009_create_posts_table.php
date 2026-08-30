<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Tiêu đề bài viết');
            $table->string('slug')->unique()->comment('Đường dẫn tĩnh chuẩn SEO');
            $table->text('summary')->nullable()->comment('Mô tả ngắn gọn');
            $table->longText('content')->comment('Nội dung bài viết');
            $table->string('thumbnail')->nullable()->comment('Ảnh đại diện bài viết');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete()->comment('Tác giả');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->comment('Danh mục bài viết');
            
            // Cache columns
            $table->unsignedBigInteger('views_count')->default(0)->index()->comment('Lượt xem (Cache)');
            
            // SEO
            $table->string('meta_title')->nullable()->comment('Tiêu đề SEO');
            $table->string('meta_description')->nullable()->comment('Mô tả SEO');
            
            $table->enum('status', ['published', 'draft'])->default('published')->index()->comment('Trạng thái');
            $table->timestamp('published_at')->nullable()->index()->comment('Ngày giờ xuất bản');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down() { Schema::dropIfExists('posts'); }
};