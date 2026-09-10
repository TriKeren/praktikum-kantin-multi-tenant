<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. orders (Platform-scoped, tanpa tenant_id)
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->uuid('customer_session_id')->nullable()->constrained('customer_sessions')->nullOnDelete();
            $table->string('public_id')->unique(); // ID yang dilihat pelanggan (contoh: ORD-123)
            $table->string('checkout_key')->unique();
            $table->string('tracking_token_hash')->unique();
            $table->enum('status', ['pending', 'paid', 'completed', 'cancelled'])->default('pending');
            $table->bigInteger('total_amount')->default(0);
            $table->json('customer_snapshot')->nullable();
            $table->timestamps();
        });

        // 2. tenant_orders (Tenant-owned, memecah pesanan per lapak)
        Schema::create('tenant_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('commission_id')->nullable()->constrained('commission_schemes')->nullOnDelete();
            $table->enum('status', ['pending', 'preparing', 'ready', 'delivered', 'cancelled'])->default('pending');
            $table->dateTime('scheduled_at')->nullable();
            
            // Snapshot nominal
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('tax_amount')->default(0);
            $table->bigInteger('commission_amount')->default(0);
            $table->bigInteger('net_amount')->default(0);
            $table->timestamps();
        });

        // 3. order_items (Item yang dipesan)
        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->restrictOnDelete();
            $table->string('name_snapshot');
            $table->bigInteger('price_snapshot');
            $table->integer('quantity');
            $table->bigInteger('modifier_total')->default(0);
            $table->bigInteger('line_total')->default(0);
            $table->timestamps();
        });

        // 4. order_item_modifiers (Opsi tambahan pada item)
        Schema::create('order_item_modifiers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('modifier_group_id')->constrained('modifier_groups')->restrictOnDelete();
            $table->foreignId('modifier_option_id')->constrained('modifier_options')->restrictOnDelete();
            $table->string('group_snapshot');
            $table->string('option_snapshot');
            $table->bigInteger('price_delta_snapshot')->default(0);
            $table->timestamps();
        });

        // 5. menu_stock_movements (Mutasi stok, memakai idempotency_key)
        Schema::create('menu_stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained()->restrictOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('idempotency_key')->unique(); // Mencegah proses ganda
            $table->enum('type', ['deduct', 'restock', 'void']);
            $table->integer('quantity_delta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_stock_movements');
        Schema::dropIfExists('order_item_modifiers');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('tenant_orders');
        Schema::dropIfExists('orders');
    }
};