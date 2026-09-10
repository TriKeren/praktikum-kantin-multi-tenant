# Kamus Data & Urutan Migrasi - Kantin Multi-Tenant

## 1. Kelompok Identitas & Akses (Akar Induk)
Tabel-tabel ini berdiri sendiri atau hanya saling bergantung di tingkat dasar. Wajib dibuat paling pertama.

*   **`canteens`**
    *   Scope: Platform-scoped
    *   Unique: `code`, `slug`
*   **`users`**
    *   Scope: Platform-scoped
    *   Unique: `email`, `phone_e164`
*   **`tenants`** (Bergantung pada canteens)
    *   Scope: Platform-scoped (Batas wilayah tenant)
    *   Unique: `(canteen_id, code)`, `(canteen_id, slug)`
*   **`user_canteen_roles`** (Bergantung pada users, canteens)
    *   Scope: Platform-scoped
*   **`user_tenant_roles`** (Bergantung pada users, tenants)
    *   Scope: Tenant-owned
*   **`tenant_balances`** (Bergantung pada tenants)
    *   Scope: Tenant-owned
*   **`tenant_bank_accounts`** (Bergantung pada tenants)
    *   Scope: Tenant-owned

## 2. Kelompok Meja & Sesi
*   **`dining_tables`** (Bergantung pada canteens)
    *   Scope: Platform-scoped
    *   Unique: `(canteen_id, code)`
*   **`table_qr_tokens`** (Bergantung pada dining_tables)
    *   Scope: Platform-scoped
    *   Unique: `token_hash`
*   **`customer_sessions`** (Bergantung pada canteens, table_qr_tokens)
    *   Scope: Platform-scoped
    *   Unique: `session_token_hash`

## 3. Kelompok Katalog & Konfigurasi
Seluruh tabel di kelompok ini bergantung pada tabel `tenants`.

*   **`commission_schemes`** 
    *   Scope: Tenant-owned
*   **`tenant_operating_hours`**
    *   Scope: Tenant-owned
    *   Unique: `(tenant_id, day)`
*   **`menu_categories`**
    *   Scope: Tenant-owned
    *   Unique: `(tenant_id, name)`
*   **`modifier_groups`**
    *   Scope: Tenant-owned
    *   Unique: `(tenant_id, name)`
*   **`menus`** (Bergantung pada menu_categories)
    *   Scope: Tenant-owned
*   **`modifier_options`** (Bergantung pada modifier_groups)
    *   Scope: Tenant-owned
*   **`menu_modifier_groups`** (Bergantung pada menus, modifier_groups)
    *   Scope: Tenant-owned

## 4. Kelompok Transaksi & Finansial (Anak Terbawah)
Kelompok ini merangkum data dari seluruh tabel di atas. Jangan buat tabel ini sebelum kelompok 1, 2, dan 3 selesai.

*   **`orders`** (Bergantung pada canteens, customer_sessions)
    *   Scope: Platform-scoped
    *   Unique: `public_id` / `order_number`, `checkout_key`, `tracking_token_hash`
*   **`tenant_orders`** (Bergantung pada orders, tenants, commission_schemes)
    *   Scope: Tenant-owned
*   **`order_items`** (Bergantung pada tenant_orders, menus)
    *   Scope: Tenant-owned
*   **`order_item_modifiers`** (Bergantung pada order_items, modifier_groups, modifier_options)
    *   Scope: Tenant-owned
*   **`menu_stock_movements`** (Bergantung pada menus, order_items)
    *   Scope: Tenant-owned
    *   Unique: `idempotency_key`
*   **`payments`** (Bergantung pada orders)
    *   Scope: Platform-scoped
    *   Unique: `(order_id, payment_reference)`, `idempotency_key`
*   **`payment_attempts`** (Bergantung pada payments)
    *   Scope: Platform-scoped
    *   Unique: `provider_reference`
*   **`payment_events`** (Bergantung pada payment_attempts)
    *   Scope: Platform-scoped (Append-only)
    *   Unique: `provider_event_id`
*   **`withdrawals`** (Bergantung pada tenants, tenant_bank_accounts, users)
    *   Scope: Tenant-owned
    *   Unique: `(active_tenant, idempotency_key)`
*   **`ledger_entries`** (Bergantung pada tenants, orders, payments, withdrawals)
    *   Scope: Mutasi / Append-only
    *   Unique: `idempotency_key`

## 5. Kelompok Audit & Asinkron
*   **`outbox_events`** (Bergantung pada tenants)
    *   Scope: Append-only
    *   Unique: `event_key`
*   **`notification_deliveries`** (Bergantung pada orders, customer_sessions, tenant_orders)
    *   Scope: Append-only
    *   Unique: `(event_key, channel)`
*   **`audit_logs`** (Bergantung pada users, canteens, tenants)
    *   Scope: Append-only