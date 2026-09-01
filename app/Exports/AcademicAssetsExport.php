<?php

namespace App\Exports;

use App\Models\AcademicAsset;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AcademicAssetsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private readonly string $roomType) {}

    public function query()
    {
        return AcademicAsset::query()
            ->where('room_type', $this->roomType)
            ->orderBy('location')
            ->orderBy('room')
            ->orderBy('asset_code');
    }

    public function map($asset): array
    {
        return [
            $asset->asset_code,
            $asset->asset_type,
            $asset->brand,
            $asset->model,
            $asset->serial_number,
            $asset->location,
            $asset->building,
            $asset->room,
            $asset->condition,
            $asset->status,
            $asset->os,
            $asset->processor,
            $asset->mainboard,
            $asset->memory_gb,
            $asset->hard_drive_gb,
            $asset->monitor,
            $asset->tahun_pembelian,
            $asset->notes,
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
            'Lokasi',
            'Gedung',
            'Ruangan',
            'Kondisi',
            'Status',
            'OS',
            'Processor',
            'Mainboard',
            'RAM (GB)',
            'Storage (GB)',
            'Monitor',
            'Tahun Pembelian',
            'Catatan',
        ];
    }
}
