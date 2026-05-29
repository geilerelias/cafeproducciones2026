<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $permissions = [
            'event.requests.create',
            'event.requests.view-own',
            'event.requests.manage',
        ];

        foreach ($permissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name, 'guard_name' => 'web'],
                ['created_at' => $now, 'updated_at' => $now],
            );
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->where('guard_name', 'web')
            ->pluck('id', 'name');

        $rolePermissions = [
            'cliente' => ['event.requests.create', 'event.requests.view-own'],
            'trabajador' => [],
            'admin' => ['event.requests.manage'],
            'superadmin' => ['event.requests.manage'],
        ];

        foreach ($rolePermissions as $roleName => $names) {
            $roleId = DB::table('roles')->where('name', $roleName)->where('guard_name', 'web')->value('id');

            if (! $roleId) {
                continue;
            }

            foreach ($names as $permissionName) {
                $permissionId = $permissionIds[$permissionName] ?? null;

                if ($permissionId) {
                    DB::table('role_has_permissions')->updateOrInsert([
                        'permission_id' => $permissionId,
                        'role_id' => $roleId,
                    ]);
                }
            }
        }

        app('cache')->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $names = ['event.requests.create', 'event.requests.view-own', 'event.requests.manage'];

        $permissionIds = DB::table('permissions')
            ->whereIn('name', $names)
            ->where('guard_name', 'web')
            ->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('role_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('model_has_permissions')->whereIn('permission_id', $permissionIds)->delete();
            DB::table('permissions')->whereIn('id', $permissionIds)->delete();
        }

        app('cache')->forget(config('permission.cache.key'));
    }
};
