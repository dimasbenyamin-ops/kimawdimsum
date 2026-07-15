<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'system_stock'   => 'decimal:4',
        'physical_stock' => 'decimal:4',
        'variance'       => 'decimal:4',
        'variance_cost'  => 'decimal:2',
    ];

    /**
     * Get the stock opname that owns the item.
     */
    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    /**
     * Get the ingredient associated with the item.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
