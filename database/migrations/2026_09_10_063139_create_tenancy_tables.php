<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. canteens (Induk Utama - Platform Scoped)
        Schema::create('canteens', function (Blueprint $table): void {
            $table->id(); // Otomatis menggunakan BIGINT unsigned
            $table->string('code', 30)->unique();
            $table->string('slug', 100)->unique();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('service_fee_rate', 5, 2)->default(0);
            $table->timestamps(6); // Timestamps dengan presisi mikrodetik
        });

        // 2. tenants (Anak dari canteens - Platform Scoped)
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('canteen_id')->constrained()->restrictOnDelete();
            $table->string('code', 30);
            $table->string('slug', 100);
            $table->string('display_name', 120);
            $table->enum('status', ['pending', 'active', 'suspended', 'inactive']);
            $table->timestamps(6);
            $table->softDeletes(5);
            
            // Constraint pengamanan ganda
            $table->unique(['canteen_id', 'code']);
            $table->unique(['canteen_id', 'slug']);
            $table->unique(['id', 'canteen_id']);
        });

        // 3. user_canteen_roles (Anak dari users & canteens)
        Schema::create('user_canteen_roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('canteen_id')->constrained()->cascadeOnDelete();
            $table->string('role', 50);
            $table->timestamps(6);
        });

        // 4. user_tenant_roles (Anak dari users & tenants)
        Schema::create('user_tenant_roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('role', 50);
            $table->timestamps(6);
        });

        // 5. tenant_balances (Anak dari tenants)
        Schema::create('tenant_balances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            // Nominal uang disimpan sebagai BIGINT (integer presisi tetap), bukan FLOAT
            $table->bigInteger('available_amount')->default(0); 
            $table->bigInteger('held_amount')->default(0);
            $table->timestamps(6);
        });

        // 6. tenant_bank_accounts (Anak dari tenants)
        Schema::create('tenant_bank_accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            
            // Menyimpan nomor rekening asli yang dienkripsi dan 4 digit terakhir yang aman ditampilkan
            $table->text('account_number_cipher'); 
            $table->string('account_number_last_four', 4); 
            
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->boolean('is_primary')->default(false);
            $table->timestamps(6);
        });
    }

    public function down(): void
    {
        // Penghapusan tabel harus dibalik dari anak (bawah) ke induk (atas) agar tidak melanggar foreign key
        Schema::dropIfExists('tenant_bank_accounts');
        Schema::dropIfExists('tenant_balances');
        Schema::dropIfExists('user_tenant_roles');
        Schema::dropIfExists('user_canteen_roles');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('canteens');
    }
};