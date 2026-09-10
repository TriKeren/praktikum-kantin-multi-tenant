<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. payments
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->string('payment_reference')->nullable();
            $table->string('idempotency_key')->unique();
            $table->bigInteger('amount');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('settled_at')->nullable();
            $table->unique(['order_id', 'payment_reference']);
            $table->timestamps();
        });

        // 2. ledger_entries (Mutasi saldo tenant - Append Only)
        Schema::create('ledger_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('idempotency_key')->unique();
            $table->enum('type', ['credit', 'debit', 'hold', 'release']);
            $table->bigInteger('available_delta')->default(0);
            $table->bigInteger('held_delta')->default(0);
            $table->timestamp('created_at')->useCurrent(); // Tabel append-only umumnya tidak butuh updated_at
        });

        // 3. outbox_events (Antrean proses asinkron - Append Only)
        Schema::create('outbox_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('event_key')->unique();
            $table->string('aggregate_type');
            $table->json('payload');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 4. audit_logs (Riwayat aktivitas - Append Only)
        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('canteen_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('entity_type');
            $table->string('action');
            $table->json('before_snapshot')->nullable();
            $table->json('after_snapshot')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('outbox_events');
        Schema::dropIfExists('ledger_entries');
        Schema::dropIfExists('payments');
    }
};