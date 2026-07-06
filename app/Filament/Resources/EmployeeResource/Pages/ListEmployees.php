<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Exports\EmployeesExport;
use App\Filament\Resources\EmployeeResource;
use App\Imports\EmployeesImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->url(fn (): string => static::getResource()::getUrl('create')), Actions\Action::make('export')->label('Export Excel')->icon('heroicon-o-arrow-down-tray')->color('success')->action(function () {
            return Excel::download(new EmployeesExport, 'employees.xlsx');
        }), Actions\Action::make('import')->label('Import Excel')->icon('heroicon-o-arrow-up-tray')->color('info')->form([FileUpload::make('file')->disk('public')->directory('imports')->required()->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])])->action(function (array $data) {
            $filePath = storage_path('app/public/'.$data['file']);
            if (! file_exists($filePath)) {
                throw new \Exception('File tidak ditemukan di path: '.$filePath);
            } Excel::import(new EmployeesImport, $filePath);
            Notification::make()->title('Import berhasil!')->success()->send();
        }), ];
    }
}
