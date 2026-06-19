<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'quantity'    => 'decimal:4',
        'cost_amount' => 'decimal:2',
        'waste_date'  => 'date',
    ];

    /**
     * Get the ingredient that was wasted.
     */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Get the unit used for the waste quantity.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(IngredientUnit::class, 'unit_id');
    }

    /**
     * Get the user who logged the waste.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
