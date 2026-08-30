<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique()->index()->comment('Mã đơn hàng hiển thị');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('Khách mua hàng');
            
            // Shipping Info
            $table->string('customer_name')->comment('Tên người nhận');
            $table->string('customer_phone')->comment('SĐT người nhận');
            $table->string('customer_email')->nullable();
            $table->text('shipping_address')->comment('Địa chỉ giao hàng đầy đủ');
            $table->text('order_notes')->nullable()->comment('Ghi chú của khách');
            
            // Pricing
            $table->decimal('subtotal', 15, 2)->comment('Tổng tiền hàng');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('Tiền được giảm');
            $table->decimal('shipping_fee', 15, 2)->default(0)->comment('Phí vận chuyển');
            $table->decimal('total_amount', 15, 2)->comment('Tổng tiền phải thanh toán');
            
            // Statuses
            $table->enum('status', ['pending', 'processing', 'shipping', 'completed', 'cancelled', 'refunded'])->default('pending')->index()->comment('Trạng thái đơn');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])->default('unpaid')->index()->comment('Trạng thái thanh toán');
            $table->string('payment_method')->default('cod')->comment('Phương thức thanh toán');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down() { Schema::dropIfExists('orders'); }
};