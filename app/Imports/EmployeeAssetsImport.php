<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\EmployeeAsset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class EmployeeAssetsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    public function model(array $row)
    {
        $nik = $row['nik'] ?? '';
        $employee = Employee::query()->where('nik', $nik)->first();

        if (!$employee) {
            return null;
        }

        return new EmployeeAsset([
            'employee_id'     => $employee->id,
            'asset_code'      => $row['asset_code'] ?? null,
            'asset_type'      => $row['jenis_perangkat'] ?? null,
            'brand'           => $row['brand'] ?? null,
            'model'           => $row['model'] ?? null,
            'serial_number'   => $row['serial_number'] ?? null,
            'location'        => $row['lokasi'] ?? null,
            'os'              => $row['os'] ?? null,
            'processor'       => $row['processor'] ?? null,
            'mainboard'       => $row['mainboard'] ?? null,
            'memory_gb'       => $row['ram_gb'] ?? null,
            'hard_drive_gb'   => $row['storage_gb'] ?? null,
            'monitor'         => $row['monitor'] ?? null,
            'tahun_pembelian' => $row['tahun_pembelian'] ?? null,
            'condition'       => $row['kondisi'] ?? 'Baik',
            'assigned_at'     => $row['tanggal_diberikan'] ?? null,
            'notes'           => $row['catatan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'asset_code' => 'required|unique:employee_assets,asset_code',
            'nik' => 'required',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning('Import row gagal', [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
            ]);
        }
    }
}
