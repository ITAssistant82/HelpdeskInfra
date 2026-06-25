<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    public function query()
    {
        return Employee::query();
    }

    public function map($employee): array
    {
        return [
            $employee->nik,
            $employee->inisial,
            $employee->email,
            $employee->full_name,
            $employee->prodi_unit_kerja,
            $employee->employee_group,
            $employee->work_contract,
            $employee->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'NIK',
            'Inisial',
            'Email',
            'Full Name',
            'Prodi / Unit Kerja',
            'Employee Group',
            'Work Contract',
            'Created At',
        ];
    }
}
