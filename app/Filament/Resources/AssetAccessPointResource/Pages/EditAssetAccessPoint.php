<?php

namespace App\Filament\Resources\AssetAccessPointResource\Pages;

use App\Filament\Resources\AssetAccessPointResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetAccessPoint extends EditRecord
{
    protected static string $resource = AssetAccessPointResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
