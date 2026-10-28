<?php

use App\Models\EmployeeAsset;

uses(Tests\TestCase::class);

it('summarizes SSD and harddisk independently', function () {
    expect((new EmployeeAsset(['ssd_gb' => 512]))->storage_summary)->toBe('SSD: 512 GB');
    expect((new EmployeeAsset(['hard_drive_gb' => 1000]))->storage_summary)->toBe('Harddisk: 1000 GB');
    expect((new EmployeeAsset(['ssd_gb' => 512, 'hard_drive_gb' => 1000]))->storage_summary)
        ->toBe('SSD: 512 GB | Harddisk: 1000 GB');
    expect((new EmployeeAsset)->storage_summary)->toBe('');
});

it('can apply and roll back the SSD migration without changing existing storage', function () {
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
    Illuminate\Support\Facades\DB::purge('sqlite');

    Illuminate\Support\Facades\Schema::create('employee_assets', function ($table) {
        $table->id();
        $table->decimal('hard_drive_gb', 8, 2)->nullable();
    });
    Illuminate\Support\Facades\DB::table('employee_assets')->insert(['hard_drive_gb' => 1000]);
    $migration = require __DIR__.'/../../database/migrations/2026_10_20_000001_add_ssd_gb_to_employee_assets_table.php';
    $migration->up();
    expect(Illuminate\Support\Facades\Schema::hasColumn('employee_assets', 'ssd_gb'))->toBeTrue();
    Illuminate\Support\Facades\DB::table('employee_assets')->where('id', 1)->update(['ssd_gb' => 512]);
    $asset = EmployeeAsset::first();
    expect((float) $asset->ssd_gb)->toBe(512.0);
    expect((float) $asset->hard_drive_gb)->toBe(1000.0);
    $migration->down();
    expect(Illuminate\Support\Facades\Schema::hasColumn('employee_assets', 'ssd_gb'))->toBeFalse();
});
