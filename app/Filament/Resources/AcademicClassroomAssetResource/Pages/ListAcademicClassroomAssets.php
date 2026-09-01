<?php

namespace App\Filament\Resources\AcademicClassroomAssetResource\Pages;

use App\Exports\AcademicAssetsExport;
use App\Filament\Resources\AcademicClassroomAssetResource;
use App\Imports\AcademicAssetsImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListAcademicClassroomAssets extends ListRecords
{
    protected static string $resource = AcademicClassroomAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')->label('Export Excel')->icon('heroicon-o-arrow-down-tray')->color('success')->action(
                fn () => Excel::download(new AcademicAssetsExport('classroom'), 'aset_perangkat_kelas.xlsx')
            ),
            Actions\Action::make('import')->label('Import Excel')->icon('heroicon-o-arrow-up-tray')->color('info')->visible(AcademicClassroomAssetResource::canCreate())->form([
                FileUpload::make('file')->disk('public')->directory('imports')->required()->acceptedFileTypes([
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]),
            ])->action(function (array $data): void {
                Excel::import(new AcademicAssetsImport('classroom'), storage_path('app/public/'.$data['file']));
                Notification::make()->title('Import aset perangkat kelas berhasil')->success()->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}
