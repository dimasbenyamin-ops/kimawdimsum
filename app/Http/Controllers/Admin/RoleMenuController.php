<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavMenu;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RoleMenuController
 *
 * Manages the assignment of NavMenu items to Roles.
 * The Administrator uses this screen (from the mockup) to check/uncheck
 * which backend sections each Role can access.
 *
 * Uses Eloquent's `sync()` method on the BelongsToMany relationship,
 * which atomically replaces the pivot records — no raw SQL.
 */
class RoleMenuController extends Controller
{
    /**
     * Show all nav menus with checkboxes indicating current assignment for a Role.
     */
    public function index(Role $role): View
    {
        $navMenus = NavMenu::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        // IDs already assigned to this role
        $assignedIds = $role->navMenus()->pluck('nav_menus.id')->toArray();

        return view('admin.role-menu.index', compact('role', 'navMenus', 'assignedIds'));
    }

    /**
     * Sync the NavMenu assignments for a Role.
     *
     * The `sync()` method removes any pivot rows not in the submitted list
     * and inserts new ones — a single atomic operation.
     * Submitting an empty array (unchecking all) revokes all access.
     */
    public function sync(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            // Array of nav_menu IDs; each must exist in the nav_menus table
            'nav_menu_ids'   => ['nullable', 'array'],
            'nav_menu_ids.*' => ['integer', 'exists:nav_menus,id'],
        ]);

        // sync() with empty array removes all assignments (fail-closed)
        $role->navMenus()->sync($validated['nav_menu_ids'] ?? []);

        return redirect()->route('admin.role-menu.index', $role)
                         ->with('success', 'Hak akses menu untuk role "' . e($role->name) . '" berhasil diperbarui.');
    }
}
