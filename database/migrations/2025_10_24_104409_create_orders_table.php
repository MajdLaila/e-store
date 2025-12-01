<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->string('shipping_address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // اقتراح: لو بدك تدعم عدة منتجات بالطلب، بدّل هذا و أنشئ order_items table (موصوف لاحقاً)
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
