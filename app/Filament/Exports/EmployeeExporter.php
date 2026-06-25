<?php

namespace App\Filament\Exports;

use App\Models\Employee;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EmployeeExporter extends Exporter
{
    protected static ?string $model = Employee::class;
    public static function getColumns(): array
    {
        return [ExportColumn::make('nik'), ExportColumn::make('inisial'), ExportColumn::make('email'), ExportColumn::make('full_name')->label('Full Name'), ExportColumn::make('prodi_unit_kerja')->label('Prodi / Unit Kerja'), ExportColumn::make('employee_group')->label('Employee Group'), ExportColumn::make('work_contract')->label('Work Contract'), ExportColumn::make('created_at')->label('Created At'), ];
    }
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export employee selesai dan '. number_format($export->successful_rows). ' baris berhasil diekspor.';
        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '. number_format($failedRowsCount). ' baris gagal diekspor.';
        }
        return $body;
    }
}
