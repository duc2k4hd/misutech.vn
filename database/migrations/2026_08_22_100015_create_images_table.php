<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            
            // Polymorphic relation columns
            $table->morphs('imageable'); // Creates imageable_id (BIGINT) and imageable_type (VARCHAR) with Index
            
            $table->string('url')->comment('Tên ảnh chấm với đuôi (vd: image.png)');
            $table->string('path')->comment('Link ảnh chạy trên web trừ domain, không có / ở đầu (vd: storage/images/image.png)');
            $table->string('alt')->nullable()->comment('Thẻ alt chuẩn SEO');
            $table->text('notes')->nullable()->comment('Ghi chú');
            $table->integer('position')->default(0)->comment('Thứ tự sắp xếp ảnh');
            
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('images');
    }
};
