<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. dining_tables
        Schema::create('dining_tables', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->string('code', 30);
            $table->string('label', 100)->nullable();
            $table->string('zone', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unique(['canteen_id', 'code']);
            $table->timestamps();
        });

        // 2. table_qr_tokens
        Schema::create('table_qr_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dining_table_id')->constrained()->cascadeOnDelete();
            // BINARY(32) digunakan agar ukuran tetap dan perbandingan hash lebih cepat
            $table->binary('token_hash', 32)->unique(); 
            $table->enum('status', ['active', 'revoked'])->default('active');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        // 3. customer_sessions
        Schema::create('customer_sessions', function (Blueprint $table): void {
            $table->uuid('id')->primary(); // Memakai UUID
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->foreignId('table_qr_token_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_token_hash')->unique();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_sessions');
        Schema::dropIfExists('table_qr_tokens');
        Schema::dropIfExists('dining_tables');
    }
};