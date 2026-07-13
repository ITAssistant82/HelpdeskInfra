<?php

namespace App\Filament\Widgets;

use App\Models\EmployeeAsset;
use Filament\Widgets\ChartWidget;

class AssetTypeChart extends ChartWidget
{
    protected static ?int $sort = 8;
    protected ?string $heading = 'Asset per Jenis Perangkat';
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected function getData(): array
    {
        $laptop = Employeeasset::where('asset_type', 'Laptop')->count();
        $pc = Employeeasset::where('asset_type', 'PC')->count();
        $smartphone = Employeeasset::where('asset_type', 'Smartphone')->count();
        $pribadi = Employeeasset::where('asset_type', 'Pribadi')->count();
        return ['datasets' => [['label' => 'Jumlah Asset', 'data' => [$laptop, $pc, $smartphone, $pribadi], 'backgroundColor' => ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'], 'borderColor' => '#ffffff', 'borderWidth' => 2, ], ], 'labels' => ['Laptop', 'PC', 'Smartphone', 'Pribadi'], ];
    }
    protected function getType(): string
    {
        return 'doughnut';
    }
}
