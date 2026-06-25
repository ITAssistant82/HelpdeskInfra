<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'helpdesk_l1', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'it_infra_l1', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'it_infra_l2', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'it_infra_l3', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'network_team', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'm365_team', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'security_soc', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'approver', 'guard_name' => 'web']);
    }
}
