<?php

namespace App\Filament\Resources\AssetSwitchCilandakResource\Pages;

use App\Filament\Resources\AssetSwitchCilandakResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetSwitchCilandak extends CreateRecord
{
    protected static string $resource = AssetSwitchCilandakResource::class;
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
