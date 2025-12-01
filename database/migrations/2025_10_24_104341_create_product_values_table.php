<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_key_id')->constrained('product_keys')->cascadeOnDelete();
            $table->string('product_value'); // example: "أسود", "128GB"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_values');
    }
};
