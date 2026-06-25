<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Employee([
            'nik'              => $row['nik'] ?? $row['nip'] ?? null,
            'email'            => $row['email'] ?? $row['e_mail'] ?? null,
            'inisial'          => $row['inisial'] ?? $row['initial'] ?? null,
            'full_name'        => $row['full_name'] ?? $row['nama'] ?? $row['name'] ?? null,
            'prodi_unit_kerja' => $row['prodi_unit_kerja'] ?? $row['prodiunit_kerja'] ?? $row['prodi'] ?? $row['unit_kerja'] ?? '-',
            'employee_group'   => $row['employee_group'] ?? $row['grup_karyawan'] ?? $row['group'] ?? '-',
            'work_contract'    => $row['work_contract'] ?? $row['kontrak_kerja'] ?? $row['contract'] ?? '-',
        ]);
    }
}