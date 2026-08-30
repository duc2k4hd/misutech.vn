<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            
            // Snapshot of product data at purchase time
            $table->string('product_name')->comment('Tên sản phẩm lúc mua');
            $table->decimal('price', 15, 2)->comment('Giá mua');
            $table->integer('quantity')->comment('Số lượng');
            $table->decimal('total', 15, 2)->comment('Thành tiền (price * quantity)');
            
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('order_items'); }
};