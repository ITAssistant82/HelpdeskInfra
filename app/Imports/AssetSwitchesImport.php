<?php

namespace App\Imports;

use App\Models\AssetSwitch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class AssetSwitchesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    protected $location;

    public function __construct($location = null)
    {
        $this->location = $location;
    }

    public function model(array $row)
    {
        $serialNumber = !empty($row['serial_number']) ? $row['serial_number'] : null;

        if ($serialNumber && AssetSwitch::where('serial_number', $serialNumber)->exists()) {
            \Log::warning("Import skip: serial_number '{$serialNumber}' sudah ada", [
                'row' => $row['host_name'] ?? '-',
            ]);
            return null;
        }

        return new AssetSwitch([
            'host_name'      => $row['host_name'] ?? null,
            'ip'             => $row['ip'] ?? null,
            'network_device' => $row['network_device'] ?? null,
            'stacking'       => $row['stacking'] ?? null,
            'snmp'           => $row['snmp'] ?? null,
            'brand'          => $row['brand'] ?? null,
            'type'           => $row['type'] ?? null,
            'series'         => $row['series'] ?? null,
            'remote_type'    => $row['remote_type'] ?? null,
            'username'       => $row['username'] ?? null,
            'password'       => $row['password'] ?? null,
            'location'       => $this->location ?? ($row['location'] ?? null),
            'ruangan'        => $row['ruangan'] ?? null,
            'tower'          => $row['tower'] ?? null,
            'uplink_port'    => $row['uplink_port'] ?? null,
            'uplink_switch'  => $row['uplink_switch'] ?? null,
            'downlink_port'  => $row['downlink_port'] ?? null,
            'serial_number'  => $serialNumber,
            'keterangan'     => $row['keterangan'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'host_name' => 'required',
            'ip' => 'required',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            \Log::warning('Import row gagal', [
                'row' => $failure->row(),
                'errors' => $failure->errors(),
            ]);
        }
    }
}
