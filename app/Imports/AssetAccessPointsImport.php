<?php

namespace App\Imports;

use App\Models\AssetAccessPoint;
use App\Models\AssetAccessPointItem;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;

class AssetAccessPointsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    protected $location;

    public function __construct($location = null)
    {
        $this->location = $location;
    }

    public function model(array $row)
    {
        $location = $this->location ?? ($row['location'] ?? null);

        if (!$location || empty($row['host_name'])) {
            return null;
        }

        $summary = AssetAccessPoint::firstOrCreate(
            [
                'location' => $location,
                'nama_gedung' => $row['nama_gedung'] ?? '',
                'lantai' => $row['lantai'] ?? '',
            ],
            [
                'location' => $location,
                'nama_gedung' => $row['nama_gedung'] ?? '',
                'lantai' => $row['lantai'] ?? '',
            ]
        );

        return new AssetAccessPointItem([
            'asset_access_point_id' => $summary->id,
            'host_name' => $row['host_name'],
            'ip' => $row['ip'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'host_name' => 'required',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            \Log::warning('Import AP row gagal', [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
            ]);
        }
    }
}
