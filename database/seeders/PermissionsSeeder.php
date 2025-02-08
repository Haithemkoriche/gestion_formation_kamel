<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define Permissions
        $permissions = [
            'view_categories',
            'edit_categories',
            'delete_categories',
            'create_categories',
        ];

        // Create Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);

        // Assign All Permissions to Super Admin
        $superAdminRole->syncPermissions(Permission::all());

        // Assign Specific Permissions to Admin
        $adminRole->syncPermissions(['view_categories']);
    }
}
