<?php

namespace App\Filament\Resources\AcademicLabAssetResource\Pages;

use App\Exports\AcademicAssetsExport;
use App\Filament\Resources\AcademicLabAssetResource;
use App\Imports\AcademicAssetsImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListAcademicLabAssets extends ListRecords
{
    protected static string $resource = AcademicLabAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')->label('Export Excel')->icon('heroicon-o-arrow-down-tray')->color('success')->action(
                fn () => Excel::download(new AcademicAssetsExport('lab'), 'aset_perangkat_lab.xlsx')
            ),
            Actions\Action::make('import')->label('Import Excel')->icon('heroicon-o-arrow-up-tray')->color('info')->visible(AcademicLabAssetResource::canCreate())->form([
                FileUpload::make('file')->disk('public')->directory('imports')->required()->acceptedFileTypes([
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]),
            ])->action(function (array $data): void {
                Excel::import(new AcademicAssetsImport('lab'), storage_path('app/public/'.$data['file']));
                Notification::make()->title('Import aset perangkat lab berhasil')->success()->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
