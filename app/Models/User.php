<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * App\Models\User
 *
 * Represents internal staff users (Administrator, Kasir).
 * Guest customers do NOT have a User record — they place orders
 * as unauthenticated visitors, tracked via session only.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $username     Unique login handle for staff
 * @property string      $email
 * @property int|null    $role_id      FK → roles.id (null = no backend access)
 * @property string|null $phone
 * @property string|null $avatar
 * @property Carbon|null $expired_at   Tanggal Exp User — access denied after this date
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    // -------------------------------------------------------
    // Mass Assignment
    // -------------------------------------------------------

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'phone',
        'avatar',
        'expired_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'expired_at'        => 'datetime',
        ];
    }

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    /**
     * The Role assigned to this staff user.
     * Null means the user has no backend access.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Orders processed/confirmed by this cashier or admin.
     */
    public function processedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'processed_by');
    }

    /**
     * Food menu items created by this admin.
     */
    public function createdMenus(): HasMany
    {
        return $this->hasMany(Menu::class, 'created_by');
    }

    // -------------------------------------------------------
    // Authorization Helpers
    // -------------------------------------------------------

    /**
     * Check whether this user's role has access to a specific
     * backend route/section (via the role_menu pivot).
     *
     * This is the primary authorization gate for backend middleware.
     * Always fail-closed: returns false if role is not loaded or missing.
     *
     * @param  string  $routeName  The named route to check, e.g. "admin.users.index"
     */
    public function hasNavMenuAccess(string $routeName): bool
    {
        if (! $this->role) {
            return false; // No role assigned — deny
        }

        // Eager-load navMenus if not already loaded
        $this->loadMissing('role.navMenus');

        // 1. Exact match
        if ($this->role->navMenus->contains('route_name', $routeName)) {
            return true;
        }

        // 2. Resource route fallback (e.g., admin.users.store -> admin.users.index)
        // Check if removing the last segment and appending '.index' matches an allowed route
        $parts = explode('.', $routeName);
        if (count($parts) > 1) {
            array_pop($parts); // remove the action (store, create, edit, update, destroy, etc.)
            
            $baseRouteIndex = implode('.', $parts) . '.index';
            if ($this->role->navMenus->contains('route_name', $baseRouteIndex)) {
                return true;
            }

            // Fallback for non-resource parent routes (e.g., admin.reports.sales-per-menu.destroy -> admin.reports.sales-per-menu)
            $baseRouteExact = implode('.', $parts);
            if ($this->role->navMenus->contains('route_name', $baseRouteExact)) {
                return true;
            }
        }

        // 3. Custom prefix fallbacks
        // If the route starts with cashier. and cashier.dashboard is allowed
        if (str_starts_with($routeName, 'cashier.') && $this->role->navMenus->contains('route_name', 'cashier.dashboard')) {
            return true;
        }

        if (str_starts_with($routeName, 'admin.settings.store_identity.') && $this->role->navMenus->contains('route_name', 'admin.settings.store_identity')) {
            return true;
        }

        if (str_starts_with($routeName, 'admin.role-menu.') && $this->role->navMenus->contains('route_name', 'admin.roles.index')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user's account has expired.
     * An expired user must NOT be allowed into the backend.
     */
    public function isExpired(): bool
    {
        return $this->expired_at !== null && $this->expired_at->isPast();
    }

    /**
     * True if this user has any role assigned (i.e., is internal staff).
     */
    public function isStaff(): bool
    {
        return $this->role_id !== null;
    }
}
