<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Supplier
 *
 * Master data for ingredient suppliers / vendors.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $contact_person
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $notes
 * @property bool        $is_active
 */
class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'address',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * Purchases from this supplier.
     * (Will be used in Fase 3)
     */
    // public function purchases(): HasMany
    // {
    //     return $this->hasMany(Purchase::class);
    // }

    // -------------------------------------------------------
    // Scopes
    // -------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (! $keyword) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('contact_person', 'like', "%{$keyword}%")
              ->orWhere('phone', 'like', "%{$keyword}%");
        });
    }
}
