<?php

namespace App\Filament\Widgets;

use App\Models\AssetSwitch;
use Filament\Widgets\ChartWidget;

class SwitchBySiteChart extends ChartWidget
{
    protected ?string $heading = 'Network Switch per Site';
    protected static ?int $sort = 3;
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected function getData(): array
    {
        $bsd = AssetSwitch::where('location', 'BSD')->count();
        $cilandak = AssetSwitch::where('location', 'Cilandak')->count();
        return ['datasets' => [['label' => 'Jumlah Switch', 'data' => [$bsd, $cilandak], 'backgroundColor' => ['#06b6d4', '#14b8a6'], 'borderColor' => '#ffffff', 'borderWidth' => 2, ], ], 'labels' => ['BSD', 'Cilandak'], ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
}
