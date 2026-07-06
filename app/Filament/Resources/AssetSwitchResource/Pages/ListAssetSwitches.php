<?php

namespace App\Filament\Resources\AssetSwitchResource\Pages;

use App\Exports\AssetSwitchesExport;
use App\Filament\Resources\AssetSwitchResource;
use App\Imports\AssetSwitchesImport;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListAssetSwitches extends ListRecords
{
    protected static string $resource = AssetSwitchResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\Action::make('export')->label('Export Excel')->icon('heroicon-o-arrow-down-tray')->color('success')->action(function () {
            return Excel::download(new AssetSwitchesExport, 'asset_switches.xlsx');
        }), Actions\Action::make('import')->label('Import Excel')->icon('heroicon-o-arrow-up-tray')->color('info')->form([FileUpload::make('file')->disk('public')->directory('imports')->required()->acceptedFileTypes(['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])])->action(function (array $data) {
            $filePath = storage_path('app/public/'.$data['file']);
            if (! file_exists($filePath)) {
                throw new \Exception('File tidak ditemukan di path: '.$filePath);
            }
            Excel::import(new AssetSwitchesImport, $filePath);
            Notification::make()->title('Import berhasil!')->success()->send();
        }), Actions\CreateAction::make()->url(fn (): string => static::getResource()::getUrl('create')), ];
    }
}
