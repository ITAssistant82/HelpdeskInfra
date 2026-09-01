<?php

namespace App\Filament\Resources\AcademicLabAssetResource\Pages;

use App\Filament\Resources\AcademicLabAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcademicLabAsset extends EditRecord
{
    protected static string $resource = AcademicLabAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['room_type'] = AcademicLabAssetResource::getRoomType();

        return $data;
    }
}
