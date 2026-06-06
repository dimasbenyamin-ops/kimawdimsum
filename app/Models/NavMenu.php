<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\NavMenu
 *
 * Represents a backend sidebar/navigation menu item.
 * Named `NavMenu` (table: `nav_menus`) to avoid collision with
 * the `Menu` model (food/dimsum items, table: `menus`).
 *
 * @property int         $id
 * @property string      $name        Display label, e.g. "User"
 * @property string      $route_name  Named route, e.g. "admin.users.index"
 * @property string|null $icon        Optional icon class
 * @property int|null    $parent_id   FK to nav_menus.id for grouped menus
 * @property int         $sort_order  Sidebar display order
 */
class NavMenu extends Model
{
    protected $fillable = [
        'name',
        'route_name',
        'icon',
        'parent_id',
        'sort_order',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * Roles that have been granted access to this nav menu item.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_menu',
            'nav_menu_id',
            'role_id'
        );
    }

    /**
     * Child menu items (sub-navigation).
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavMenu::class, 'parent_id')
                    ->orderBy('sort_order');
    }

    /**
     * Parent menu item (if this is a sub-menu).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavMenu::class, 'parent_id');
    }
}
