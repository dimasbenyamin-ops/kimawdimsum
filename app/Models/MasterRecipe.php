<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\MasterRecipe
 *
 * Represents a base recipe or semi-finished product.
 * e.g., "Dimsum Original (Per Biji)", "Dimsum Mentai (Per Biji)"
 */
class MasterRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * Ingredients required for ONE base unit of this master recipe.
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'master_recipe_items')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Menus that use this master recipe.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_master_recipes')
                    ->withPivot('multiplier')
                    ->withTimestamps();
    }
}
