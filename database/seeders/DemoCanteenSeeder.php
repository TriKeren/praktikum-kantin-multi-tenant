<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DemoCanteenSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Buat 1 Kantin Utama
        $canteenId = DB::table('canteens')->insertGetId([
            'code' => 'KNT-01',
            'slug' => 'kantin-pusat',
            'tax_rate' => 11.00,
            'service_fee_rate' => 0.00,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Buat 2 Tenant (Penyewa)
        $tenant1Id = DB::table('tenants')->insertGetId([
            'canteen_id' => $canteenId,
            'code' => 'TNT-01',
            'slug' => 'warung-bunda',
            'display_name' => 'Warung Bunda',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $tenant2Id = DB::table('tenants')->insertGetId([
            'canteen_id' => $canteenId,
            'code' => 'TNT-02',
            'slug' => 'ayam-geprek-juara',
            'display_name' => 'Ayam Geprek Juara',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Buat 2 Meja Makan
        DB::table('dining_tables')->insert([
            ['canteen_id' => $canteenId, 'code' => 'TBL-01', 'label' => 'Meja 01', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['canteen_id' => $canteenId, 'code' => 'TBL-02', 'label' => 'Meja 02', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 4. Buat Kategori Menu untuk Masing-masing Tenant
        $cat1Id = DB::table('menu_categories')->insertGetId([
            'tenant_id' => $tenant1Id, 'name' => 'Makanan Utama', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now
        ]);
        $cat2Id = DB::table('menu_categories')->insertGetId([
            'tenant_id' => $tenant2Id, 'name' => 'Spesial Ayam', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now
        ]);

        // 5. Buat Menu untuk Masing-masing Tenant
        DB::table('menus')->insert([
            [
                'tenant_id' => $tenant1Id, 
                'category_id' => $cat1Id, 
                'name' => 'Nasi Goreng Spesial', 
                'base_price' => 15000, 
                'is_available' => true, 
                'created_at' => $now, 
                'updated_at' => $now
            ],
            [
                'tenant_id' => $tenant2Id, 
                'category_id' => $cat2Id, 
                'name' => 'Ayam Geprek Level 5', 
                'base_price' => 18000, 
                'is_available' => true, 
                'created_at' => $now, 
                'updated_at' => $now
            ],
        ]);

        $this->call([
            DemoCanteenSeeder::class,
        ]);
    }
}