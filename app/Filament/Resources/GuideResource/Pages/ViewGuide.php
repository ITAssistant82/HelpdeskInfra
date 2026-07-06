<?php

namespace App\Filament\Resources\GuideResource\Pages;

use App\Filament\Resources\GuideResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewGuide extends ViewRecord
{
    protected static string $resource = GuideResource::class;

    protected string $view = 'filament.resources.guide-resource.pages.view-guide';

    protected function hasBreadcrumbs(): bool
    {
        return false;
    }

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()->visible(fn (): bool => Auth::user()?->hasAnyRole(['super_admin', 'admin']))];
    }
}
