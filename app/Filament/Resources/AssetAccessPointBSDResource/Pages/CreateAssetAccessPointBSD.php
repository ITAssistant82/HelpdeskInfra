<?php

namespace App\Filament\Resources\AssetAccessPointBSDResource\Pages;

use App\Filament\Resources\AssetAccessPointBSDResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetAccessPointBSD extends CreateRecord
{
    protected static string $resource = AssetAccessPointBSDResource::class;
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
