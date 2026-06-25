<?php

namespace App\Filament\Resources\AssetAccessPointCilandakResource\Pages;

use App\Filament\Resources\AssetAccessPointCilandakResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetAccessPointCilandak extends CreateRecord
{
    protected static string $resource = AssetAccessPointCilandakResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data ['location'] = 'Cilandak';
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
