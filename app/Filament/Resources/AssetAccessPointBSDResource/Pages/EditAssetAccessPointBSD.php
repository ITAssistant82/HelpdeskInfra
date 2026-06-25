<?php

namespace App\Filament\Resources\AssetAccessPointBSDResource\Pages;

use App\Filament\Resources\AssetAccessPointBSDResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetAccessPointBSD extends EditRecord
{
    protected static string $resource = AssetAccessPointBSDResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make(), ];
    }
}
