<?php

namespace App\Filament\Resources\AssetAccessPointResource\Pages;

use App\Filament\Resources\AssetAccessPointResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetAccessPoint extends CreateRecord
{
    protected static string $resource = AssetAccessPointResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
