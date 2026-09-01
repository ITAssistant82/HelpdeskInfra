<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicClassroomAssetResource\Pages;
use App\Filament\Support\AcademicAssetResource;

class AcademicClassroomAssetResource extends AcademicAssetResource
{
    protected static string $roomType = 'classroom';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static ?string $navigationLabel = 'Aset Perangkat Kelas';

    protected static ?string $modelLabel = 'Aset Perangkat Kelas';

    protected static ?string $pluralModelLabel = 'Aset Perangkat Kelas';

    protected static ?int $navigationSort = 2;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicClassroomAssets::route('/'),
            'create' => Pages\CreateAcademicClassroomAsset::route('/create'),
            'edit' => Pages\EditAcademicClassroomAsset::route('/{record}/edit'),
        ];
    }
}
