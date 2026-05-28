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
                'identification_type' => 'cc',
                'identification_number' => '1000000001',
                'phone' => '3000000001',
                'email' => User::SUPER_ADMIN_EMAIL,
                'role' => 'superadmin',
            ],
            [
                'name' => 'Administrador Cafe',
                'identification_type' => 'cc',
                'identification_number' => '1000000002',
                'phone' => '3000000002',
                'email' => 'admin@cafeproducciones.test',
                'role' => 'admin',
            ],
            [
                'name' => 'Trabajador Demo',
                'identification_type' => 'cc',
                'identification_number' => '1000000003',
                'phone' => '3000000003',
                'email' => 'trabajador@cafeproducciones.test',
                'role' => 'trabajador',
            ],
            [
                'name' => 'Cliente Demo',
                'identification_type' => 'cc',
                'identification_number' => '1000000004',
                'phone' => '3000000004',
                'email' => 'cliente@cafeproducciones.test',
                'role' => 'cliente',
            ],
        ];

        foreach ($users as $seedUser) {
            $user = User::query()->firstOrCreate([
                'email' => $seedUser['email'],
            ], [
                'name' => $seedUser['name'],
                'identification_type' => $seedUser['identification_type'],
                'identification_number' => $seedUser['identification_number'],
                'phone' => $seedUser['phone'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            $user->syncRoles([$seedUser['role']]);
            $user->syncPermissions([]);
        }

        $superAdmin = User::query()->firstOrCreate([
            'email' => User::SUPER_ADMIN_EMAIL,
        ], [
            'name' => 'Geiler Elias',
            'identification_type' => 'cc',
            'identification_number' => '1000000001',
            'phone' => '3000000001',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $superAdmin->syncRoles(['superadmin']);
        $superAdmin->syncPermissions([]);

        $this->call(DemoOperationsSeeder::class);
    }
}
