<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAsset;
use App\Models\AssetStock;
use App\Models\AssetAccessPoint;
use App\Models\AssetAccessPointItem;
use App\Models\AssetSwitch;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isLimited = $user && $user->hasRole('user');

        $totalEmployees = Employee::count();
        $totalAssets = EmployeeAsset::count();

        $assetBaik = EmployeeAsset::where('condition', 'Baik')->count();
        $assetPerawatan = EmployeeAsset::where('condition', 'Perlu Perawatan')->count();
        $assetRusak = EmployeeAsset::where('condition', 'Rusak')->count();

        $assetTypes = EmployeeAsset::selectRaw('asset_type, count(*) as total')
            ->groupBy('asset_type')
            ->pluck('total', 'asset_type');

        $latestAssets = EmployeeAsset::with('employee')
            ->latest()
            ->take(5)
            ->get();

        if ($isLimited) {
            return view('dashboard', compact(
                'totalEmployees',
                'totalAssets',
                'assetBaik',
                'assetPerawatan',
                'assetRusak',
                'assetTypes',
                'latestAssets'
            ));
        }

        $totalStock = AssetStock::count();
        $totalAccessPoints = AssetAccessPoint::count();
        $totalAccessPointItems = AssetAccessPointItem::count();
        $totalSwitches = AssetSwitch::count();

        $stockCondition = AssetStock::selectRaw('condition, count(*) as total')
            ->groupBy('condition')
            ->pluck('total', 'condition');

        $stockTypes = AssetStock::selectRaw('asset_type, count(*) as total')
            ->groupBy('asset_type')
            ->pluck('total', 'asset_type');

        $switchBSD = AssetSwitch::where('location', 'BSD')->count();
        $switchCilandak = AssetSwitch::where('location', 'Cilandak')->count();
        $switchBrands = AssetSwitch::selectRaw('brand, count(*) as total')
            ->groupBy('brand')
            ->pluck('total', 'brand');

        $apItemsBSD = AssetAccessPointItem::whereHas('assetAccessPoint', function ($q) {
            $q->where('location', 'BSD');
        })->count();

        $apItemsCilandak = AssetAccessPointItem::whereHas('assetAccessPoint', function ($q) {
            $q->where('location', 'Cilandak');
        })->count();

        return view('dashboard', compact(
            'totalEmployees',
            'totalAssets',
            'totalStock',
            'totalAccessPoints',
            'totalAccessPointItems',
            'totalSwitches',
            'assetBaik',
            'assetPerawatan',
            'assetRusak',
            'assetTypes',
            'latestAssets',
            'stockCondition',
            'stockTypes',
            'switchBSD',
            'switchCilandak',
            'switchBrands',
            'apItemsBSD',
            'apItemsCilandak'
        ));
    }
}
