<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (User::DEFAULT_PERMISSIONS as $roleName => $permissions) {
            $role = Role::findOrCreate($roleName);

            $role->syncPermissions([]);

            foreach (array_filter($permissions, fn (string $permission) => $permission !== '*') as $permissionName) {
                $permission = Permission::findOrCreate($permissionName);
                $role->givePermissionTo($permission);
            }
        }

        $users = [
            [
                'name' => 'Geiler Elias',
                'email' => User::SUPER_ADMIN_EMAIL,
                'role' => 'superadmin',
            ],
            [
                'name' => 'Administrador Cafe',
                'email' => 'admin@cafeproducciones.test',
                'role' => 'admin',
            ],
            [
                'name' => 'Trabajador Demo',
                'email' => 'trabajador@cafeproducciones.test',
                'role' => 'trabajador',
            ],
            [
                'name' => 'Cliente Demo',
                'email' => 'cliente@cafeproducciones.test',
                'role' => 'cliente',
            ],
        ];

        foreach ($users as $seedUser) {
            $user = User::query()->firstOrCreate([
                'email' => $seedUser['email'],
            ], [
                'name' => $seedUser['name'],
                'password' => Hash::make('password'),
            ]);

            $user->syncRoles([$seedUser['role']]);
            $user->syncPermissions([]);
        }

        $superAdmin = User::query()->firstOrCreate([
            'email' => User::SUPER_ADMIN_EMAIL,
        ], [
            'name' => 'Geiler Elias',
            'password' => Hash::make('password'),
        ]);

        $superAdmin->syncRoles(['superadmin']);
        $superAdmin->syncPermissions([]);

        $this->call(DemoOperationsSeeder::class);
    }
}
