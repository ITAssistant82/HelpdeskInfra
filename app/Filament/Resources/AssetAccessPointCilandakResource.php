<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetAccessPointCilandakResource\Pages;
use App\Models\AssetAccessPoint;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssetAccessPointCilandakResource extends Resource
{
    protected static ?string $model = AssetAccessPoint::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';
    protected static ?string $navigationLabel = 'Cilandak';
    protected static string|\UnitEnum|null $navigationGroup = 'Asset Access Point';
    protected static ?int $navigationSort = 2;
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_asset_access_point_cilandak'));
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('create_asset_access_point_cilandak'));
    }
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_asset_access_point_cilandak'));
    }
    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_asset_access_point_cilandak'));
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('location', 'Cilandak')->withCount('items');
    }
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\TextInput::make('nama_gedung')->label('Nama Gedung')->required(), Forms\Components\TextInput::make('lantai')->label('Lantai')->required(), Forms\Components\Repeater::make('items')->relationship('items')->schema([Forms\Components\TextInput::make('host_name')->label('Host Name')->required(), Forms\Components\TextInput::make('ip')->label('IP'), Forms\Components\TextInput::make('product_name')->label('Product Name')->nullable(), Forms\Components\DatePicker::make('eol_announcement')->label('EOL Announcement')->nullable(), Forms\Components\DatePicker::make('end_of_sale')->label('End of Sale (EOS)')->nullable(), Forms\Components\DatePicker::make('end_of_service_life')->label('End of Service Life (EOSL)')->nullable(), ])->addActionLabel('Tambah Access Point')->defaultItems(0)->collapsible()->itemLabel(fn (array $state): ?string => $state ['host_name'] ?? null), Forms\Components\Hidden::make('location')->default('Cilandak'), ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('nama_gedung')->label('Nama Gedung')->searchable()->sortable(), Tables\Columns\TextColumn::make('lantai')->label('Lantai')->searchable()->sortable(), Tables\Columns\TextColumn::make('items_count')->label('Jumlah')->counts('items')->sortable()->action(Actions\Action::make('view_items')->label('')->icon('heroicon-o-eye')->modalHeading(fn ($record) => 'Access Point: '. $record->nama_gedung. ' - '. $record->lantai)->modalContent(fn ($record) => view('components.access-point-items', ['items' => $record->items]))->modalSubmitAction(false)->modalCancelActionLabel('Tutup')->modalWidth('lg')), ])->filters([Tables\Filters\SelectFilter::make('nama_gedung')->label('Gedung')->options(fn () => AssetAccessPoint::where('location', 'Cilandak')->whereNotNull('nama_gedung')->distinct()->pluck('nama_gedung', 'nama_gedung')->toArray()), ])->actions([Actions\EditAction::make(), ])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make(), ]), ]);
    }
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return ['index' => Pages\ListAssetAccessPointCilandaks::route('/'), 'create' => Pages\CreateAssetAccessPointCilandak::route('/create'), 'edit' => Pages\EditAssetAccessPointCilandak::route('/{record}/edit'), ];
    }
}
