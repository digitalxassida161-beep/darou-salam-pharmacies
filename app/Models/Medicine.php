<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    protected $fillable = [
        'license_id',
        'name',
        'form',
        'strength',
        'category',
        'unit',
        'sale_price',
        'minimum_stock',
        'active',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function lots(): HasMany
    {
        return $this->hasMany(MedicineLot::class);
    }

    public function getStockAttribute(): int
    {
        return (int) $this->lots()
            ->whereDate('expiry_date', '>=', today())
            ->sum('quantity');
    }
}
