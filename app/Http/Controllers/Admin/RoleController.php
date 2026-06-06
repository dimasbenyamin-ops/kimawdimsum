<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RoleController
 *
 * Manages the list of available Roles (e.g. Administrator, Kasir).
 * Roles are created/renamed by the Administrator.
 * Deleting a role sets affected users' role_id to NULL (nullOnDelete FK),
 * effectively revoking their backend access (fail-closed).
 */
class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:roles,name'],
        ]);

        Role::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.roles.index')
                         ->with('success', 'Role "' . e($validated['name']) . '" berhasil ditambahkan.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100', "unique:roles,name,{$role->id}"],
        ]);

        $role->update(['name' => $validated['name']]);

        return redirect()->route('admin.roles.index')
                         ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        // Deleting a role will NULL-out affected users' role_id (FK nullOnDelete)
        // which means those users lose all backend access immediately (fail-closed).
        $name = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
                         ->with('success', 'Role "' . e($name) . '" berhasil dihapus. Pengguna terkait kehilangan akses.');
    }
}
