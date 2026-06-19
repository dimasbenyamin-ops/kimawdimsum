<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\IngredientUnit
 *
 * Measurement units for ingredients with optional conversion to a base unit.
 *
 * @property int         $id
 * @property string      $name              e.g. "kilogram"
 * @property string      $abbreviation      e.g. "kg"
 * @property int|null    $base_unit_id      FK to self (null = is base unit)
 * @property float       $conversion_factor Multiplier to convert to base unit
 */
class IngredientUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'abbreviation',
        'base_unit_id',
        'conversion_factor',
    ];

    protected function casts(): array
    {
        return [
            'conversion_factor' => 'decimal:6',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The base unit this unit converts to.
     */
    public function baseUnit(): BelongsTo
    {
        return $this->belongsTo(self::class, 'base_unit_id');
    }

    /**
     * Units that use this unit as their base.
     */
    public function derivedUnits(): HasMany
    {
        return $this->hasMany(self::class, 'base_unit_id');
    }

    /**
     * Ingredients that use this unit.
     */
    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'unit_id');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Whether this unit is a base unit (has no parent).
     */
    public function isBaseUnit(): bool
    {
        return $this->base_unit_id === null;
    }

    /**
     * Display label: "kilogram (kg)"
     */
    public function getLabelAttribute(): string
    {
        return "{$this->name} ({$this->abbreviation})";
    }
}
