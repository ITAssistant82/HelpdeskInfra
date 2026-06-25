<?php

namespace App\Filament\Widgets;

use App\Models\AssetStock;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStockOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected function getStats(): array
    {
        $totalStock = AssetStock::count();
        $stockBaik = AssetStock::where('condition', 'Baik')->count();
        $stockPerawatan = AssetStock::where('condition', 'Perlu Perawatan')->count();
        $stockRusak = AssetStock::where('condition', 'Rusak')->count();
        $laptop = AssetStock::where('asset_type', 'Laptop')->count();
        $pc = AssetStock::where('asset_type', 'PC')->count();
        return [Stat::make('Total Asset Stock (Belum Terpakai)', $totalStock)->description('IT asset yang belum di-assign')->descriptionIcon('heroicon-o-archive-box')->color('info'), Stat::make('Stock Kondisi Baik', $stockBaik)->description($totalStock > 0 ? round(($stockBaik / $totalStock) * 100). '% dari total stock' : '0%')->descriptionIcon('heroicon-o-check-circle')->color('success'), Stat::make('Stock Laptop', $laptop)->description('Total laptop di stock')->descriptionIcon('heroicon-o-computer-desktop')->color('warning'), Stat::make('Stock PC', $pc)->description('Total PC di stock')->descriptionIcon('heroicon-o-server')->color('gray'), ];
    }
}
