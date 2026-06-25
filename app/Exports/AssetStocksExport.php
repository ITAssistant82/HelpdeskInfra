<?php

namespace App\Exports;

use App\Models\AssetStock;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssetStocksExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return AssetStock::query();
    }

    public function map($stock): array
    {
        return [
            $stock->asset_code,
            $stock->asset_type,
            $stock->brand,
            $stock->model,
            $stock->serial_number,
            $stock->condition,
            $stock->os,
            $stock->processor,
            $stock->mainboard,
            $stock->memory_gb,
            $stock->hard_drive_gb,
            $stock->monitor,
            $stock->tahun_pembelian,
            $stock->location,
            $stock->notes,
            $stock->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Asset Code',
            'Jenis Perangkat',
            'Brand',
            'Model',
            'Serial Number',
            'Kondisi',
            'OS',
            'Processor',
            'Mainboard',
            'RAM (GB)',
            'Storage (GB)',
            'Monitor',
            'Tahun Pembelian',
            'Lokasi Penyimpanan',
            'Catatan',
            'Created At',
        ];
    }
}
