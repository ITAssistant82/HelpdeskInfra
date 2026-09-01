<?php

namespace App\Filament\Resources\AcademicClassroomAssetResource\Pages;

use App\Filament\Resources\AcademicClassroomAssetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAcademicClassroomAsset extends EditRecord
{
    protected static string $resource = AcademicClassroomAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['room_type'] = AcademicClassroomAssetResource::getRoomType();

        return $data;
    }
}
