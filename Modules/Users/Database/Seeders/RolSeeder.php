<?php

namespace Modules\Users\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissionCollection = collect([

        ]);

        $allPermissions = $permissionCollection->collapse();
        $allPermissions->map(function (string $permission) {
            Permission::create(['name' => $permission]);
        });

        Role::create(['name' => 'Administrador'])
            ->givePermissionTo($allPermissions);
        Role::create(['name' => 'Super Admin'])
            ->givePermissionTo($allPermissions);
    }
}
