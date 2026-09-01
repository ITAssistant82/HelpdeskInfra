<?php

namespace App\Filament\Resources\AcademicLabAssetResource\Pages;

use App\Filament\Resources\AcademicLabAssetResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicLabAsset extends CreateRecord
{
    protected static string $resource = AcademicLabAssetResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['room_type'] = AcademicLabAssetResource::getRoomType();

        return $data;
    }
}
