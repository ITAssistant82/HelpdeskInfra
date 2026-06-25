<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            'ticket', 'ticket_category', 'ticket_layer',
            'asset_switch', 'asset_switch_bsd', 'asset_switch_cilandak',
            'asset_access_point', 'asset_access_point_bsd', 'asset_access_point_cilandak',
            'asset_stock', 'employee', 'employee_asset',
            'guide', 'activity_log', 'user', 'role', 'permission',
        ];

        $actions = ['view_any', 'create', 'update', 'delete'];

        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$resource}", 'guard_name' => 'web']);
            }
        }

        // Super Admin gets all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        // Admin gets all permissions too
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $this->command->info('Permissions seeded: ' . count($resources) * count($actions));
    }
}
