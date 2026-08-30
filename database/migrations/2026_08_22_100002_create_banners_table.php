<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Tiêu đề banner');
            $table->string('image')->comment('Đường dẫn ảnh');
            $table->string('link')->nullable()->comment('Đường dẫn khi click');
            $table->integer('position')->default(0)->comment('Thứ tự hiển thị');
            $table->enum('status', ['active', 'draft'])->default('active')->index()->comment('Trạng thái');
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('banners'); }
};