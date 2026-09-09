<?php

use Illuminate\Support\Facades\Route;

/**
 * Konteks OPERATOR TENANT (internal). Prefix: tenant/{tenant}, name: tenant.*
 * Middleware auth+verified+role:tenant dipasang di bootstrap/app.php.
 * Katalog/KDS tenant diisi Modul 7 & 12; scopeBindings pada Modul 4.
 */
// Contoh di routes/admin.php
Route::get('/dashboard', function () {
    return view('layouts.tenant');
})->name('dashboard')->middleware('can:access-tenant');
