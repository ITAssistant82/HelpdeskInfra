<?php

namespace App\Filament\Widgets;

use App\Models\ASsetaccesspointitem;
use Filament\Widgets\ChartWidget;

class AccessPointBySiteChart extends ChartWidget
{
    protected ?string $heading = 'Access Point per Site';
    protected static ?int $sort = 5;
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected function getData(): array
    {
        $bsd = AssetAccessPointItem::whereHas('assetAccessPoint', function ($q) {
            $q->where('location', 'BSD');
        })->count();
        $cilandak = AssetAccessPointItem::whereHas('assetAccessPoint', function ($q) {
            $q->where('location', 'Cilandak');
        })->count();
        return ['datasets' => [['label' => 'Jumlah Access Point', 'data' => [$bsd, $cilandak], 'backgroundColor' => ['#06b6d4', '#14b8a6'], 'borderColor' => '#ffffff', 'borderWidth' => 2, ], ], 'labels' => ['BSD', 'Cilandak'], ];
    }
    protected function getType(): string
    {
        return 'bar';
    }
}
