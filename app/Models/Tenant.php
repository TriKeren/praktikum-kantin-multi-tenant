<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    protected $guarded = ['id'];

    // Aturan konversi tipe data sesuai instruksi Modul 3
    protected function casts(): array
    {
        return [
            'status' => 'string', // Enum
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Pembuatan relasi eksplisit
    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }
}