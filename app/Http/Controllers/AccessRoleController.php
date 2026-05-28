<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessRoleController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAccess($request);

        return Inertia::render('Access/Roles', [
            'roles' => Role::query()
                ->with('permissions:id,name')
                ->withCount('users')
                ->orderBy('name')
                ->get()
                ->map(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions' => $role->permissions->pluck('name')->values(),
                    'users_count' => $role->users_count,
                ]),
            'availablePermissions' => Permission::query()->orderBy('name')->pluck('name'),
            'canManageProtectedRoles' => $request->user()->isSuperAdmin(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $this->validated($request);
        $role = Role::findOrCreate($validated['name']);
        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', 'Rol guardado.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeAccess($request);
        abort_if(
            ! $request->user()->isSuperAdmin() && in_array($role->name, ['admin', 'superadmin'], true),
            403,
            'Solo el superadmin puede modificar roles administrativos.',
        );

        $validated = $this->validated($request, $role);

        if (! in_array($role->name, User::ROLES, true)) {
            $role->update(['name' => $validated['name']]);
        }

        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', 'Rol actualizado.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeAccess($request);
        abort_if(in_array($role->name, User::ROLES, true), 422, 'No se pueden eliminar roles base.');

        $role->delete();

        return back()->with('success', 'Rol eliminado.');
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:80',
                Rule::unique('roles', 'name')->ignore($role?->id),
            ],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ]);
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless($request->user()?->canAccess('roles.manage'), 403);
    }
}
