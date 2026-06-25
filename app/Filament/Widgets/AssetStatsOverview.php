<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Employeeasset;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AssetStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    public static function canView(): bool
    {
        return auth()->user()?->isStaff() ?? false;
    }
    protected function getStats(): array
    {
        $totalEmployees = Employee::count();
        $totalAssets = Employeeasset::count();
        $assetsBaik = Employeeasset::where('condition', 'Baik')->count();
        $assetsRusak = Employeeasset::where('condition', 'Rusak')->count();
        $assetsPerawatan = Employeeasset::where('condition', 'Perlu Perawatan')->count();
        return [Stat::make('Total Karyawan', $totalEmployees)->description('Jumlah seluruh karyawan')->descriptionIcon('heroicon-o-users')->color('info'), Stat::make('Total Asset', $totalAssets)->description('Jumlah seluruh perangkat')->descriptionIcon('heroicon-o-computer-desktop')->color('warning'), Stat::make('Kondisi Baik', $assetsBaik)->description($totalAssets > 0 ? round(($assetsBaik / $totalAssets) * 100). '% dari total asset' : '0% dari total asset')->descriptionIcon('heroicon-o-check-circle')->color('success'), Stat::make('Perlu Perawatan', $assetsPerawatan)->description($totalAssets > 0 ? round(($assetsPerawatan / $totalAssets) * 100). '% dari total asset' : '0% dari total asset')->descriptionIcon('heroicon-o-exclamation-triangle')->color('warning'), Stat::make('Rusak', $assetsRusak)->description($totalAssets > 0 ? round(($assetsRusak / $totalAssets) * 100). '% dari total asset' : '0% dari total asset')->descriptionIcon('heroicon-o-x-circle')->color('danger'), ];
    }
}
