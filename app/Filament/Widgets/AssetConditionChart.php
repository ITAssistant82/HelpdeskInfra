<?php

namespace App\Filament\Widgets;

use App\Models\Employeeasset;
use Filament\Widgets\ChartWidget;

class AssetConditionChart extends ChartWidget
{
    protected static ?int $sort = 7;
    protected ?string $heading = 'Asset per Kondisi';
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected function getData(): array
    {
        $baik = Employeeasset::where('condition', 'Baik')->count();
        $perawatan = Employeeasset::where('condition', 'Perlu Perawatan')->count();
        $rusak = Employeeasset::where('condition', 'Rusak')->count();
        return ['datasets' => [['label' => 'Jumlah Asset', 'data' => [$baik, $perawatan, $rusak], 'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'], 'borderColor' => '#ffffff', 'borderWidth' => 1, ], ], 'labels' => ['Baik', 'Perlu Perawatan', 'Rusak'], ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
}
