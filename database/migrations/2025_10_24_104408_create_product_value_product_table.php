<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_key_value', function (Blueprint $table) {
            // composite primary: product_value_id + product_id
            $table->foreignId('product_value_id')->constrained('product_values')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['product_value_id', 'product_id'], 'pv_product_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_key_value');
    }
};
