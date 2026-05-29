<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $permission = 'event.requests.tasks.view-assigned';

        DB::table('permissions')->updateOrInsert(
            ['name' => $permission, 'guard_name' => 'web'],
            ['created_at' => $now, 'updated_at' => $now],
        );

        $permissionId = DB::table('permissions')
            ->where('name', $permission)
            ->where('guard_name', 'web')
            ->value('id');

        $roleId = DB::table('roles')->where('name', 'trabajador')->where('guard_name', 'web')->value('id');

        if ($permissionId && $roleId) {
            DB::table('role_has_permissions')->updateOrInsert([
                'permission_id' => $permissionId,
                'role_id' => $roleId,
            ]);
        }

        app('cache')->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $permissionId = DB::table('permissions')
            ->where('name', 'event.requests.tasks.view-assigned')
            ->where('guard_name', 'web')
            ->value('id');

        if ($permissionId) {
            DB::table('role_has_permissions')->where('permission_id', $permissionId)->delete();
            DB::table('model_has_permissions')->where('permission_id', $permissionId)->delete();
            DB::table('permissions')->where('id', $permissionId)->delete();
        }

        app('cache')->forget(config('permission.cache.key'));
    }
};
