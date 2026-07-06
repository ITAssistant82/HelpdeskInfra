<?php

namespace App\Filament\Resources\AssetSwitchCilandakResource\Pages;

use App\Filament\Resources\AssetSwitchCilandakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetSwitchCilandak extends EditRecord
{
    protected static string $resource = AssetSwitchCilandakResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
