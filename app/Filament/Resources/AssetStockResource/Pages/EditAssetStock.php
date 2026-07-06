<?php

namespace App\Filament\Resources\AssetStockResource\Pages;

use App\Filament\Resources\AssetStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetStock extends EditRecord
{
    protected static string $resource = AssetStockResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
