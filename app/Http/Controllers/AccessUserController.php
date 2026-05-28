<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessUserController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAccess($request);

        return Inertia::render('Access/Users', [
            'users' => User::query()
                ->with(['roles:id,name', 'permissions:id,name'])
                ->latest()
                ->get(['id', 'name', 'email', 'created_at'])
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->effective_role,
                    'roles' => $user->roles->pluck('name')->values(),
                    'permissions' => $user->permissions->pluck('name')->values(),
                    'effective_role' => $user->effective_role,
                    'effective_permissions' => $user->effective_permissions,
                    'created_at' => $user->created_at,
                ]),
            'roles' => Role::query()->orderBy('name')->pluck('name'),
            'availablePermissions' => $this->availablePermissions(),
            'canManageProtectedUsers' => $request->user()->isSuperAdmin(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeAccess($request);
        $actor = $request->user();

        abort_if(
            ! $actor->isSuperAdmin() && $user->hasAnyRole(['admin', 'superadmin']),
            403,
            'Solo el superadmin puede modificar administradores o superadmins.',
        );

        $validated = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::in(Role::query()->pluck('name')->all())],
        ]);

        abort_if(
            ! $actor->isSuperAdmin() && filled(array_intersect($validated['roles'], ['admin', 'superadmin'])),
            403,
            'Solo el superadmin puede asignar roles administrativos.',
        );

        if ($user->isSuperAdmin()) {
            $validated['roles'] = ['superadmin'];
        }

        $user->syncRoles($validated['roles']);

        return back()->with('success', 'Roles actualizados.');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);
        $actor = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', Rule::in(Role::query()->pluck('name')->all())],
        ]);

        abort_if(
            ! $actor->isSuperAdmin() && filled(array_intersect($validated['roles'], ['admin', 'superadmin'])),
            403,
            'Solo el superadmin puede crear usuarios administrativos.',
        );

        if (strtolower($validated['email']) === User::SUPER_ADMIN_EMAIL) {
            abort_unless($actor->isSuperAdmin(), 403);
            $validated['roles'] = ['superadmin'];
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->syncRoles($validated['roles']);

        return back()->with('success', 'Usuario creado.');
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless($request->user()?->canAccess('users.manage'), 403);
    }

    private function availablePermissions(): array
    {
        return Permission::query()->orderBy('name')->pluck('name')->all();
    }
}
