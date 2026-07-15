<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'name',
        'purchase_date',
        'quantity',
        'price_per_item',
        'total_price',
        'condition',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity' => 'integer',
        'price_per_item' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];
}
