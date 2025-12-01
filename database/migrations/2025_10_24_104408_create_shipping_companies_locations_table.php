<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_companies_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('lang', 10, 7)->nullable(); // longitude (you named it lang)
            $table->decimal('lat', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_companies_locations');
    }
};
