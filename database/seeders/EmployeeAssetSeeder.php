<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeAsset;
use Illuminate\Database\Seeder;

class EmployeeAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa employee existing
        $employees = Employee::limit(3)->get();

        foreach ($employees as $employee) {
            // Create 1-3 assets per employee
            EmployeeAsset::factory()
                ->count(random_int(1, 3))
                ->for($employee)
                ->create();
        }
    }
}
