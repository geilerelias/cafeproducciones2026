<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $permissionIds = [];
        $roleIds = [];

        foreach (User::DEFAULT_PERMISSIONS as $roleName => $permissions) {
            $roleIds[$roleName] = DB::table('roles')->updateOrInsert(
                ['name' => $roleName, 'guard_name' => 'web'],
                ['created_at' => $now, 'updated_at' => $now],
            );

            $roleIds[$roleName] = DB::table('roles')
                ->where('name', $roleName)
                ->where('guard_name', 'web')
                ->value('id');

            foreach (array_filter($permissions, fn (string $permission) => $permission !== '*') as $permissionName) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $permissionName, 'guard_name' => 'web'],
                    ['created_at' => $now, 'updated_at' => $now],
                );

                $permissionIds[$permissionName] = DB::table('permissions')
                    ->where('name', $permissionName)
                    ->where('guard_name', 'web')
                    ->value('id');

                DB::table('role_has_permissions')->updateOrInsert([
                    'role_id' => $roleIds[$roleName],
                    'permission_id' => $permissionIds[$permissionName],
                ]);
            }
        }

        foreach (DB::table('users')->orderBy('id')->cursor() as $user) {
            $roleName = strtolower($user->email) === User::SUPER_ADMIN_EMAIL
                ? 'superadmin'
                : ($user->role ?? 'cliente');

            if (! isset($roleIds[$roleName])) {
                $roleName = 'cliente';
            }

            DB::table('model_has_roles')->updateOrInsert([
                'role_id' => $roleIds[$roleName],
                'model_type' => User::class,
                'model_id' => $user->id,
            ]);

            $legacyPermissions = json_decode($user->permissions ?? '[]', true) ?: [];
            foreach (array_filter($legacyPermissions, fn (string $permission) => $permission !== '*' && isset($permissionIds[$permission])) as $permissionName) {
                DB::table('model_has_permissions')->updateOrInsert([
                    'permission_id' => $permissionIds[$permissionName],
                    'model_type' => User::class,
                    'model_id' => $user->id,
                ]);
            }
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', fn ($table) => $table->dropColumn('role'));
        }

        if (Schema::hasColumn('users', 'permissions')) {
            Schema::table('users', fn ($table) => $table->dropColumn('permissions'));
        }

        app('cache')->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', fn ($table) => $table->string('role')->default('cliente')->after('password'));
        }

        if (! Schema::hasColumn('users', 'permissions')) {
            Schema::table('users', fn ($table) => $table->json('permissions')->nullable()->after('role'));
        }
    }
};
