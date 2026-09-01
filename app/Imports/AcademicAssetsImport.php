<?php

namespace App\Imports;

use App\Models\AcademicAsset;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class AcademicAssetsImport implements SkipsOnFailure, ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    public function __construct(private readonly string $roomType) {}

    public function model(array $row): AcademicAsset
    {
        return new AcademicAsset([
            'room_type' => $this->roomType,
            'asset_code' => $row['asset_code'] ?? null,
            'asset_type' => $row['jenis_perangkat'] ?? null,
            'brand' => $row['brand'] ?? null,
            'model' => $row['model'] ?? null,
            'serial_number' => $row['serial_number'] ?? null,
            'location' => $row['lokasi'] ?? null,
            'building' => $row['gedung'] ?? null,
            'room' => $row['ruangan'] ?? null,
            'condition' => $row['kondisi'] ?? 'Baik',
            'status' => $row['status'] ?? 'Digunakan',
            'os' => $row['os'] ?? null,
            'processor' => $row['processor'] ?? null,
            'mainboard' => $row['mainboard'] ?? null,
            'memory_gb' => $row['ram_gb'] ?? null,
            'hard_drive_gb' => $row['storage_gb'] ?? null,
            'monitor' => $row['monitor'] ?? null,
            'tahun_pembelian' => $row['tahun_pembelian'] ?? null,
            'notes' => $row['catatan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'asset_code' => 'required|unique:academic_assets,asset_code',
            'jenis_perangkat' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'serial_number' => 'nullable|unique:academic_assets,serial_number',
            'lokasi' => 'required|in:BSD,Cilandak',
            'gedung' => 'required',
            'ruangan' => 'required',
            'kondisi' => 'nullable|in:Baik,Perlu Perawatan,Rusak',
            'status' => 'nullable|in:Digunakan,Cadangan,Perbaikan,Tidak Digunakan',
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            Log::warning('Import aset akademik gagal', [
                'room_type' => $this->roomType,
                'row' => $failure->row(),
                'errors' => $failure->errors(),
            ]);
        }
    }
}
