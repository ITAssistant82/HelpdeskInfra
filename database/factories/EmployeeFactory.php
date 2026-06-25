<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nik' => fake()->unique()->bothify('NIK-############'),
            'full_name' => fake()->name(),
            'inisial' => fake()->lexify('??'),
            'email' => fake()->unique()->safeEmail(),
            'prodi_unit_kerja' => fake()->randomElement(['PS Full Time', 'FM Full Time', 'IT Department', 'HR Department']),
            'employee_group' => fake()->randomElement(['PS Full Time', 'FM Full Time']),
            'work_contract' => fake()->randomElement(['PKWT', 'Permanent', 'Purnabakti', 'Magang']),
        ];
    }
}
