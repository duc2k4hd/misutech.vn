<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('rating')->comment('Điểm đánh giá (1-5 sao)');
            $table->text('comment')->nullable()->comment('Nội dung bình luận');
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('approved')->index();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('reviews'); }
};