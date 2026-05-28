<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;

class AccessPermissionController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorizeAccess($request);

        return Inertia::render('Access/Permissions', [
            'permissions' => Permission::query()
                ->withCount('roles')
                ->orderBy('name')
                ->get(['id', 'name', 'guard_name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120', 'regex:/^[a-z0-9_.-]+$/', Rule::unique('permissions', 'name')],
        ]);

        Permission::findOrCreate($validated['name']);

        return back()->with('success', 'Permiso creado.');
    }

    public function destroy(Request $request, Permission $permission): RedirectResponse
    {
        $this->authorizeAccess($request);

        abort_if($permission->roles()->exists(), 422, 'No se puede eliminar un permiso asignado a roles.');
        $permission->delete();

        return back()->with('success', 'Permiso eliminado.');
    }

    private function authorizeAccess(Request $request): void
    {
        abort_unless($request->user()?->canAccess('permissions.manage'), 403);
    }
}
