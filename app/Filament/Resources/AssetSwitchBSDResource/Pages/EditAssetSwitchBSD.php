<?php

namespace App\Filament\Resources\AssetSwitchBSDResource\Pages;

use App\Filament\Resources\AssetSwitchBSDResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetSwitchBSD extends EditRecord
{
    protected static string $resource = AssetSwitchBSDResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make(), ];
    }
}
