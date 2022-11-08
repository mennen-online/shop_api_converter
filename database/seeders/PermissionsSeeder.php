<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create default permissions
        Permission::create(['name' => 'list endpoints']);
        Permission::create(['name' => 'view endpoints']);
        Permission::create(['name' => 'create endpoints']);
        Permission::create(['name' => 'update endpoints']);
        Permission::create(['name' => 'delete endpoints']);

        Permission::create(['name' => 'list entities']);
        Permission::create(['name' => 'view entities']);
        Permission::create(['name' => 'create entities']);
        Permission::create(['name' => 'update entities']);
        Permission::create(['name' => 'delete entities']);

        Permission::create(['name' => 'list entityfields']);
        Permission::create(['name' => 'view entityfields']);
        Permission::create(['name' => 'create entityfields']);
        Permission::create(['name' => 'update entityfields']);
        Permission::create(['name' => 'delete entityfields']);

        Permission::create(['name' => 'list shops']);
        Permission::create(['name' => 'view shops']);
        Permission::create(['name' => 'create shops']);
        Permission::create(['name' => 'update shops']);
        Permission::create(['name' => 'delete shops']);

        Permission::create(['name' => 'list allshopdata']);
        Permission::create(['name' => 'view allshopdata']);
        Permission::create(['name' => 'create allshopdata']);
        Permission::create(['name' => 'update allshopdata']);
        Permission::create(['name' => 'delete allshopdata']);

        // Create user role and assign existing permissions
        $currentPermissions = Permission::all();
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo($currentPermissions);

        // Create admin exclusive permissions
        Permission::create(['name' => 'list roles']);
        Permission::create(['name' => 'view roles']);
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'update roles']);
        Permission::create(['name' => 'delete roles']);

        Permission::create(['name' => 'list permissions']);
        Permission::create(['name' => 'view permissions']);
        Permission::create(['name' => 'create permissions']);
        Permission::create(['name' => 'update permissions']);
        Permission::create(['name' => 'delete permissions']);

        Permission::create(['name' => 'list users']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'update users']);
        Permission::create(['name' => 'delete users']);

        // Create admin role and assign all permissions
        $allPermissions = Permission::all();
        $adminRole = Role::create(['name' => 'super-admin']);
        $adminRole->givePermissionTo($allPermissions);

        $user = \App\Models\User::whereEmail('admin@admin.com')->first();

        if ($user) {
            $user->assignRole($adminRole);
        }
    }
}
