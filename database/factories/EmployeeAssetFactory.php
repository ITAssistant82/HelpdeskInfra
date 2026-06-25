<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeAsset>
 */
class EmployeeAssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'asset_code' => fake()->unique()->bothify('AST-####'),
            'asset_type' => fake()->randomElement(['Laptop', 'PC']),
            'brand' => fake()->randomElement(['Dell', 'HP', 'Lenovo', 'ASUS', 'Apple']),
            'model' => fake()->bothify('?####'),
            'serial_number' => fake()->unique()->bothify('SN-##########'),
            'os' => fake()->randomElement(['Windows 11', 'Windows 10', 'Ubuntu 22.04', 'macOS 13']),
            'processor' => fake()->randomElement(['Intel Core i5', 'Intel Core i7', 'AMD Ryzen 5', 'AMD Ryzen 7']),
            'mainboard' => fake()->optional()->bothify('MB-####'),
            'memory_gb' => fake()->randomElement([4, 8, 16, 32]),
            'hard_drive_gb' => fake()->randomElement([256, 512, 1024, 2048]),
            'monitor' => fake()->optional()->randomElement(['24 inch FHD', '27 inch 4K', '32 inch QHD']),
            'tahun_pembelian' => fake()->numberBetween(2020, 2026),
            'condition' => fake()->randomElement(['Baik', 'Perlu Perawatan', 'Rusak']),
            'assigned_at' => fake()->dateTimeBetween('-2 years'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
