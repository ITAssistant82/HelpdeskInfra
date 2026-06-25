<?php

namespace App\Exports;

use App\Models\AssetAccessPoint;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AssetAccessPointsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $location;

    public function __construct($location = null)
    {
        $this->location = $location;
    }

    public function collection()
    {
        $query = AssetAccessPoint::with('items');

        if ($this->location) {
            $query->where('location', $this->location);
        }

        $records = $query->get();
        $rows = [];

        foreach ($records as $record) {
            if ($record->items->isEmpty()) {
                $rows[] = [
                    $record->location,
                    $record->nama_gedung,
                    $record->lantai,
                    '',
                    '',
                ];
            } else {
                foreach ($record->items as $item) {
                    $rows[] = [
                        $record->location,
                        $record->nama_gedung,
                        $record->lantai,
                        $item->host_name,
                        $item->ip ?? '',
                    ];
                }
            }
        }

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'Location',
            'Nama Gedung',
            'Lantai',
            'Host Name',
            'IP',
        ];
    }
}
