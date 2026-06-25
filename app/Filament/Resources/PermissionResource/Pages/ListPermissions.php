<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->url(fn (): string => static::getResource()::getUrl('create')), ];
    }
}
