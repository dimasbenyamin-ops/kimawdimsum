<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Ingredient
 *
 * Master data for raw ingredients / bahan baku.
 * Tracks current stock level and weighted average cost.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $sku
 * @property int         $unit_id
 * @property string      $category
 * @property float       $min_stock
 * @property float       $current_stock
 * @property float       $avg_cost
 * @property bool        $is_active
 */
class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    // Allow-list of valid category values
    public const VALID_CATEGORIES = [
        'bumbu', 'protein', 'sayur', 'tepung',
        'minyak', 'kemasan', 'saus', 'lainnya',
    ];

    public const CATEGORY_LABELS = [
        'bumbu'   => '🧂 Bumbu',
        'protein' => '🥩 Protein',
        'sayur'   => '🥬 Sayur',
        'tepung'  => '🌾 Tepung',
        'minyak'  => '🫒 Minyak',
        'kemasan' => '📦 Kemasan',
        'saus'    => '🍯 Saus',
        'lainnya' => '📎 Lainnya',
    ];

    protected $fillable = [
        'name',
        'sku',
        'unit_id',
        'category',
        'min_stock',
        'current_stock',
        'avg_cost',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_stock'     => 'decimal:4',
            'current_stock' => 'decimal:4',
            'avg_cost'      => 'decimal:4',
            'is_active'     => 'boolean',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The measurement unit for this ingredient.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(IngredientUnit::class, 'unit_id');
    }

    /**
     * Master recipes that use this ingredient.
     */
    public function masterRecipes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MasterRecipe::class, 'master_recipe_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // -------------------------------------------------------
    // Query Scopes
    // -------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('current_stock', '<=', 'min_stock')
                     ->where('min_stock', '>', 0);
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('sku', 'like', "%{$keyword}%");
        });
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Whether stock is below minimum threshold.
     */
    public function isLowStock(): bool
    {
        return $this->min_stock > 0 && $this->current_stock <= $this->min_stock;
    }

    /**
     * Formatted stock with unit abbreviation: "1,500.00 g"
     */
    public function getFormattedStockAttribute(): string
    {
        return number_format($this->current_stock, 2, ',', '.') . ' ' . ($this->unit?->abbreviation ?? '');
    }

    /**
     * Formatted average cost: "Rp 80,00"
     */
    public function getFormattedAvgCostAttribute(): string
    {
        return 'Rp ' . number_format($this->avg_cost, 2, ',', '.');
    }

    /**
     * Total stock value: current_stock × avg_cost
     */
    public function getStockValueAttribute(): float
    {
        return (float) $this->current_stock * (float) $this->avg_cost;
    }
}
