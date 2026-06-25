<?php

namespace App\Exports;

use App\Models\AssetSwitch;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssetSwitchesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $location;

    public function __construct($location = null)
    {
        $this->location = $location;
    }

    public function query()
    {
        return $this->location
            ? AssetSwitch::where('location', $this->location)
            : AssetSwitch::query();
    }

    public function map($switch): array
    {
        return [
            $switch->host_name,
            $switch->ip,
            $switch->network_device,
            $switch->stacking,
            $switch->snmp,
            $switch->brand,
            $switch->type,
            $switch->series,
            $switch->remote_type,
            $switch->username,
            $switch->password,
            $switch->location,
            $switch->ruangan,
            $switch->tower,
            $switch->uplink_port,
            $switch->uplink_switch,
            $switch->downlink_port,
            $switch->serial_number,
            $switch->keterangan,
            $switch->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'Host Name',
            'IP',
            'Network Device',
            'Stacking',
            'SNMP',
            'Brand',
            'Type',
            'Series',
            'Remote Type',
            'Username',
            'Password',
            'Location',
            'Ruangan',
            'Tower',
            'Uplink Port',
            'Uplink Switch',
            'Downlink Port',
            'Serial Number',
            'Keterangan',
            'Created At',
        ];
    }
}
