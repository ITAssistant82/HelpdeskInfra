<?php

namespace App\Exports;

use App\Models\EmployeeAsset;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeeAssetsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return EmployeeAsset::query()->with('employee');
    }

    public function map($asset): array
    {
        return [
            $asset->asset_code,
            $asset->employee?->nik,
            $asset->employee?->full_name,
            $asset->asset_type,
            $asset->brand,
            $asset->model,
            $asset->serial_number,
            $asset->location,
            $asset->os,
            $asset->processor,
            $asset->mainboard,
            $asset->memory_gb,
            $asset->hard_drive_gb,
            $asset->monitor,
            $asset->tahun_pembelian,
            $asset->condition,
            $asset->assigned_at?->format('Y-m-d'),
            $asset->notes,
            $asset->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Asset Code',
            'NIK',
            'Nama Employee',
            'Jenis Perangkat',
            'Brand',
            'Model',
            'Serial Number',
            'Lokasi',
            'OS',
            'Processor',
            'Mainboard',
            'RAM (GB)',
            'Storage (GB)',
            'Monitor',
            'Tahun Pembelian',
            'Kondisi',
            'Tanggal Diberikan',
            'Catatan',
            'Created At',
        ];
    }
}
