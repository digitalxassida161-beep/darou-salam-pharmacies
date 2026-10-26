<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineLot extends Model
{
    protected $fillable = ['medicine_id', 'lot_number', 'expiry_date', 'quantity', 'purchase_price', 'supplier'];

    protected $casts = [
        'expiry_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
