<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_code', 50)->unique();
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30);
            $table->string('customer_email', 120)->nullable();
            $table->string('customer_company', 180)->nullable();
            $table->string('customer_tax_code', 50)->nullable();
            $table->string('customer_address', 255)->nullable();
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('vat_percent', 5, 2)->default(10);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedInteger('items_count')->default(0);
            
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->enum('action_type', ['generated_pdf', 'printed', 'saved_online'])->default('generated_pdf');
            $table->enum('status', ['draft', 'submitted', 'contacted', 'closed'])->default('submitted');
            
            $table->timestamps();
            
            $table->index('customer_phone');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::create('quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained('quotes')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            $table->string('product_name', 255);
            $table->string('product_sku', 100)->nullable();
            $table->string('brand_name', 100)->nullable();
            $table->string('unit', 30)->default('Cái');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            
            $table->timestamps();
            
            $table->index('product_sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_items');
        Schema::dropIfExists('quotes');
    }
};
