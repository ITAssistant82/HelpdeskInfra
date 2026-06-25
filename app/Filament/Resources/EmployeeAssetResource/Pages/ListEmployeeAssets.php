<?php

namespace App\Filament\Resources\EmployeeAssetResource\Pages;

use App\Exports\EmployeeAssetsExport;
use App\Filament\Resources\EmployeeAssetResource;
use App\Imports\EmployeeAssetsImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListEmployeeAssets extends ListRecords
{
    protected static string $resource = EmployeeAssetResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_all_stickers')->label('Cetak All Stiker (PDF)')->icon('heroicon-o-tag')->color('danger')->url(route('assets.barcode.print-stickers'))->openUrlInNewTab(),
            Actions\Action::make('print_all_stickers_word')->label('Cetak All Stiker (Word)')->icon('heroicon-o-document')->color('warning')->url(route('assets.barcode.print-stickers-word'))->openUrlInNewTab(),
            Actions\Action::make('export')->label('Export Excel')->icon('heroicon-o-arrow-down-tray')->color('success')->action(function () {
                return Excel::download(new EmployeeAssetsExport(), 'employee_assets.xlsx');
            }),
            Actions\Action::make('import')->label('Import Excel')->icon('heroicon-o-arrow-up-tray')->color('info')->form([FileUpload::make('file')->disk('public')->directory('imports')->required()->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', ]), ])->action(function (array $data) {
                $filePath = storage_path('app/public/'. $data ['file']);
                if (! file_exists($filePath)) {
                    throw new \Exception("File tidak ditemukan di path: ". $filePath);
                }
                Excel::import(new EmployeeAssetsImport(), $filePath);
                Notification::make()->title('Import berhasil!')->success()->send();
            }),
            Actions\CreateAction::make()->url(fn (): string => static::getResource()::getUrl('create')),
        ];
    }
}
