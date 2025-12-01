<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_valid')->default(true);
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_show')->default(true);
            $table->decimal('hot_price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_show', 'is_valid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
