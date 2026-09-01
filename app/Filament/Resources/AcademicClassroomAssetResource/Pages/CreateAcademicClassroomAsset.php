<?php

namespace App\Filament\Resources\AcademicClassroomAssetResource\Pages;

use App\Filament\Resources\AcademicClassroomAssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicClassroomAsset extends CreateRecord
{
    protected static string $resource = AcademicClassroomAssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['room_type'] = AcademicClassroomAssetResource::getRoomType();

        return $data;
    }
}
