<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Role
 *
 * Represents an internal staff role (e.g., Administrator, Kasir).
 * Roles are NOT assigned to guest customers — they only apply to
 * authenticated internal users who access the backend dashboard.
 *
 * @property int    $id
 * @property string $name        e.g. "Administrator", "Kasir"
 * @property string $guard_name  always "web"
 */
class Role extends Model
{
    protected $fillable = ['name', 'guard_name'];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * Users assigned this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Backend navigation menus this role is permitted to access.
     * The pivot table is `role_menu`.
     */
    public function navMenus(): BelongsToMany
    {
        return $this->belongsToMany(
            NavMenu::class,
            'role_menu',
            'role_id',
            'nav_menu_id'
        );
    }
}
