<?php

namespace App\Filament\Resources\AssetSwitchResource\Pages;

use App\Filament\Resources\AssetSwitchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetSwitch extends EditRecord
{
    protected static string $resource = AssetSwitchResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make(), ];
    }
}
