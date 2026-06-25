<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $adminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();

        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
        ]);
        $admin->assignRole($adminRole);

        $this->call(TicketLayerSeeder::class);
        $this->call(TicketCategorySeeder::class);
        $this->call(TicketingGuideSeeder::class);
    }
}
