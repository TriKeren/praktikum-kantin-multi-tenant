<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. commission_schemes (Periode komisi / effective-dated)
        Schema::create('commission_schemes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->decimal('commission_rate', 5, 2);
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
            $table->timestamps();
        });

        // 2. tenant_operating_hours
        Schema::create('tenant_operating_hours', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('day'); 
            $table->time('opens_at');
            $table->time('closes_at');
            $table->unique(['tenant_id', 'day']);
            $table->timestamps();
        });

        // 3. menu_categories
        Schema::create('menu_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unique(['tenant_id', 'name']);
            $table->timestamps();
        });

        // 4. modifier_groups
        Schema::create('modifier_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->integer('min_select')->default(0);
            $table->integer('max_select')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unique(['tenant_id', 'name']);
            $table->unique(['tenant_id', 'id']); // Sasaran FK komposit
            $table->timestamps();
        });

        // 5. menus
        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->restrictOnDelete();
            $table->foreignId('category_id')->constrained('menu_categories')->restrictOnDelete();
            $table->string('name', 120);
            $table->bigInteger('base_price');
            $table->integer('stock_qty')->default(0);
            $table->boolean('is_available')->default(true);
            $table->integer('prep_mins')->default(0);
            $table->unique(['tenant_id', 'id']); // Sasaran FK komposit
            $table->timestamps();
        });

        // 6. modifier_options
        Schema::create('modifier_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('modifier_groups')->cascadeOnDelete();
            $table->string('name', 100);
            $table->bigInteger('price_delta')->default(0);
            $table->boolean('is_available')->default(true);
            
            // FK Komposit: Mencegah opsi nyasar ke grup tenant lain
            $table->foreign(['tenant_id', 'group_id'])
                  ->references(['tenant_id', 'id'])
                  ->on('modifier_groups')
                  ->cascadeOnDelete();
            $table->timestamps();
        });

        // 7. menu_modifier_groups
        Schema::create('menu_modifier_groups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('modifier_group_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            
            // FK Komposit: Mengunci agar menu dan modifier wajib dari tenant yang sama
            $table->foreign(['tenant_id', 'menu_id'])->references(['tenant_id', 'id'])->on('menus')->cascadeOnDelete();
            $table->foreign(['tenant_id', 'modifier_group_id'])->references(['tenant_id', 'id'])->on('modifier_groups')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_modifier_groups');
        Schema::dropIfExists('modifier_options');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('modifier_groups');
        Schema::dropIfExists('menu_categories');
        Schema::dropIfExists('tenant_operating_hours');
        Schema::dropIfExists('commission_schemes');
    }
};