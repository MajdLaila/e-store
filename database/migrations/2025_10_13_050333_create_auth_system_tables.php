<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- USERS TABLE ---
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_admin')->default(false); // حساب إداري
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lang', 10, 7)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // --- SOCIAL ACCOUNTS (Google / Facebook) ---
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider'); // google, facebook
            $table->string('provider_id');
            $table->json('profile')->nullable();
            $table->timestamps();
            $table->unique(['provider', 'provider_id']);
        });

        // --- EMAIL OTP ---
        Schema::create('email_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('code');
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();
        });

        // --- SANCTUM TOKENS ---
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable'); // user_id + type
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        // --- USER SESSIONS ---
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamps();
        });

        // --- FAILED LOGINS ---
        // كل سجل حالات اللوغ ان 
        Schema::create('failed_logins', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('attempted_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_logins');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('email_otps');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('users');
    }
};
