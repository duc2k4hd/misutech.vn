<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Tên danh mục');
            $table->string('slug')->unique()->comment('Đường dẫn tĩnh chuẩn SEO');
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete()->comment('Danh mục cha');
            $table->enum('type', ['product', 'post'])->default('product')->index()->comment('Loại danh mục');
            $table->string('icon')->nullable()->comment('Icon danh mục');
            $table->integer('position')->default(0)->comment('Thứ tự hiển thị');
            $table->enum('status', ['active', 'draft'])->default('active')->index()->comment('Trạng thái');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down() { Schema::dropIfExists('categories'); }
};