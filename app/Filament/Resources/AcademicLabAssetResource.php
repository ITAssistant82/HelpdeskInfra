<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicLabAssetResource\Pages;
use App\Filament\Support\AcademicAssetResource;

class AcademicLabAssetResource extends AcademicAssetResource
{
    protected static string $roomType = 'lab';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationLabel = 'Aset Perangkat Lab';

    protected static ?string $modelLabel = 'Aset Perangkat Lab';

    protected static ?string $pluralModelLabel = 'Aset Perangkat Lab';

    protected static ?int $navigationSort = 1;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicLabAssets::route('/'),
            'create' => Pages\CreateAcademicLabAsset::route('/create'),
            'edit' => Pages\EditAcademicLabAsset::route('/{record}/edit'),
        ];
    }
}
