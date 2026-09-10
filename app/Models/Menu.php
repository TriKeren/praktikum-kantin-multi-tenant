<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_available' => 'boolean', // Konversi TINYINT ke True/False
            'base_price' => 'integer',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}