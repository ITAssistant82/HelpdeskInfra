<?php

namespace App\Filament\Resources\AssetAccessPointCilandakResource\Pages;

use App\Filament\Resources\AssetAccessPointCilandakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetAccessPointCilandak extends EditRecord
{
    protected static string $resource = AssetAccessPointCilandakResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make(), ];
    }
}
