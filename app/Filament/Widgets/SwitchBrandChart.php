<?php

namespace App\Filament\Widgets;

use App\Models\AssetSwitch;
use Filament\Widgets\ChartWidget;

class SwitchBrandChart extends ChartWidget
{
    protected ?string $heading = 'Switch per Brand';
    protected static ?int $sort = 4;
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected function getData(): array
    {
        $brands = AssetSwitch::selectRaw('brand, count(*) AS total')->groupBy('brand')->pluck('total', 'brand');
        $colors = ['#f59e0b', '#3b82f6', '#ef4444', '#10b981', '#8b5cf6', '#ec4899'];
        return ['datasets' => [['label' => 'Jumlah Switch', 'data' => $brands->values()->toArray(), 'backgroundColor' => array_slice($colors, 0, $brands->count()), 'borderColor' => '#ffffff', 'borderWidth' => 2, ], ], 'labels' => $brands->keys()->map(fn ($b) => $b ?: 'Tidak diketahui')->toArray(), ];
    }
    protected function getType(): string
    {
        return 'doughnut';
    }
}
