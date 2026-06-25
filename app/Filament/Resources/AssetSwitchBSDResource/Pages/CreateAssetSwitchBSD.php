<?php

namespace App\Filament\Resources\AssetSwitchBSDResource\Pages;

use App\Filament\Resources\AssetSwitchBSDResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetSwitchBSD extends CreateRecord
{
    protected static string $resource = AssetSwitchBSDResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data ['location'] = 'BSD';
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
