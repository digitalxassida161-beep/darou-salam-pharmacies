<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['medicine_id', 'medicine_lot_id', 'user_id', 'type', 'quantity', 'note'];
}
