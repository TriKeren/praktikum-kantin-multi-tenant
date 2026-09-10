<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Canteen extends Model
{
    protected $guarded = ['id'];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }
}