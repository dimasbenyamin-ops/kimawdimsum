<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * UserController
 *
 * Handles CRUD for internal staff users, as shown in the UI mockup.
 * Fields: Username, Name, Password, Role (dropdown), Tanggal Exp User.
 *
 * Security notes:
 *  - Passwords are hashed via Laravel's built-in `hashed` cast / Hash::make
 *  - Password field is optional on update (only re-hashed if provided)
 *  - All inputs are validated with strict allow-list rules
 *  - CSRF is enforced by the framework's VerifyCsrfToken (never disabled)
 *  - No raw SQL — all queries go through Eloquent ORM
 *
 * TODO(security): Consider enforcing MFA for Administrator accounts.
 * TODO(security): Consider using OAuth provider for staff SSO (not implemented).
 */
class UserController extends Controller
{
    /**
     * Display a paginated list of staff users with their roles.
     */
    public function index(): View
    {
        $users = User::with('role')
            ->orderBy('name')
            ->paginate(15);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form to create a new staff user.
     */
    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created staff user.
     *
     * Password is required on creation and hashed before storage.
     * Role is required; only IDs from the `roles` table are accepted.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUserRequest($request);

        User::create([
            'username'   => $validated['username'],
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'password'   => Hash::make($validated['password']),
            'role_id'    => $validated['role_id'],
            'expired_at' => $validated['expired_at'] ?? null,
            'phone'      => $validated['phone'] ?? null,
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna "' . e($validated['name']) . '" berhasil ditambahkan.');
    }

    /**
     * Show the form to edit an existing staff user.
     */
    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update an existing staff user.
     *
     * Password is only updated if a new value is provided.
     * An empty password field is ignored entirely.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateUserRequest($request, $user);

        $updateData = [
            'username'   => $validated['username'],
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'role_id'    => $validated['role_id'],
            'expired_at' => $validated['expired_at'] ?? null,
            'phone'      => $validated['phone'] ?? null,
        ];

        // Only re-hash password if a new one was provided
        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna "' . e($validated['name']) . '" berhasil diperbarui.');
    }

    /**
     * Soft-delete a staff user.
     * Keeps the record for historical order data integrity.
     */
    public function destroy(User $user): RedirectResponse
    {
        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna "' . e($name) . '" berhasil dihapus.');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    /**
     * Shared validation rules for create and update.
     *
     * On update: username and email must be unique EXCEPT for the current user.
     * On update: password is nullable (only changed if provided).
     *
     * @param  Request   $request
     * @param  User|null $user     Null on create, current User model on update
     */
    private function validateUserRequest(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'username'   => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',             // only letters, numbers, dashes, underscores
                \Illuminate\Validation\Rule::unique('users', 'username')->ignore($user?->id),
            ],
            'name'       => ['required', 'string', 'min:2', 'max:100'],
            'email'      => [
                'required',
                'email:rfc,dns',
                'max:254',
                \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user?->id),
            ],
            // Required on create; optional (nullable) on update
            'password'   => [
                $user ? 'nullable' : 'required',
                'string',
                Password::min(8)->max(128),
            ],
            'role_id'    => ['required', 'integer', 'exists:roles,id'],
            'expired_at' => ['nullable', 'date', 'after:today'],
            'phone'      => ['nullable', 'string', 'max:20', 'regex:/^[\d\+\-\s\(\)]+$/'],
        ]);
    }
}
