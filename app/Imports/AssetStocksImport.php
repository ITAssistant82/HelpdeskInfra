<?php

namespace App\Imports;

use App\Models\AssetStock;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class AssetStocksImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    public function model(array $row)
    {
        return new AssetStock([
            'asset_code'      => $row['asset_code'] ?? null,
            'asset_type'      => $row['jenis_perangkat'] ?? null,
            'brand'           => $row['brand'] ?? null,
            'model'           => $row['model'] ?? null,
            'serial_number'   => $row['serial_number'] ?? null,
            'os'              => $row['os'] ?? null,
            'processor'       => $row['processor'] ?? null,
            'mainboard'       => $row['mainboard'] ?? null,
            'memory_gb'       => $row['ram_gb'] ?? null,
            'hard_drive_gb'   => $row['storage_gb'] ?? null,
            'monitor'         => $row['monitor'] ?? null,
            'tahun_pembelian' => $row['tahun_pembelian'] ?? null,
            'condition'       => $row['kondisi'] ?? 'Baik',
            'location'        => $row['lokasi'] ?? null,
            'notes'           => $row['catatan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'asset_code' => 'required|unique:asset_stocks,asset_code',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning('Import stock gagal', [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
            ]);
        }
    }
}
