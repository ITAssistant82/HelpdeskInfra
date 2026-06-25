<?php

namespace App\Filament\Resources\AssetSwitchResource\Pages;

use App\Filament\Resources\AssetSwitchResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetSwitch extends CreateRecord
{
    protected static string $resource = AssetSwitchResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
