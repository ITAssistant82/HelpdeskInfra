<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetAccessPointResource\Pages;
use App\Models\AssetAccessPoint;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AssetAccessPointResource extends Resource
{
    protected static ?string $model = AssetAccessPoint::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    protected static ?string $navigationLabel = 'All Access Point';

    protected static string|\UnitEnum|null $navigationGroup = 'Asset Access Point';

    protected static ?int $navigationSort = 0;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\Select::make('location')->label('Site')->options(['BSD' => 'BSD', 'Cilandak' => 'Cilandak'])->required(), Forms\Components\TextInput::make('nama_gedung')->label('Nama Gedung')->required(), Forms\Components\TextInput::make('lantai')->label('Lantai')->required(), Forms\Components\Repeater::make('items')->relationship('items')->schema([Forms\Components\TextInput::make('host_name')->label('Host Name')->required(), Forms\Components\TextInput::make('ip')->label('IP'), Forms\Components\TextInput::make('product_name')->label('Product Name')->nullable(), Forms\Components\DatePicker::make('eol_announcement')->label('EOL Announcement')->nullable(), Forms\Components\DatePicker::make('end_of_sale')->label('End of Sale (EOS)')->nullable(), Forms\Components\DatePicker::make('end_of_service_life')->label('End of Service Life (EOSL)')->nullable()])->addActionLabel('Tambah Access Point')->defaultItems(0)->collapsible()->itemLabel(fn (array $state): ?string => $state['host_name'] ?? null)]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('location')->label('Site')->badge()->colors(['info' => 'BSD', 'success' => 'Cilandak'])->sortable(), Tables\Columns\TextColumn::make('nama_gedung')->label('Nama Gedung')->searchable()->sortable(), Tables\Columns\TextColumn::make('lantai')->label('Lantai')->searchable()->sortable(), Tables\Columns\TextColumn::make('items_count')->label('Jumlah')->counts('items', 'id')->sortable()])->filters([Tables\Filters\SelectFilter::make('location')->label('Site')->options(['BSD' => 'BSD', 'Cilandak' => 'Cilandak'])])->actions([Actions\EditAction::make()])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAssetAccessPoints::route('/'), 'create' => Pages\CreateAssetAccessPoint::route('/create'), 'edit' => Pages\EditAssetAccessPoint::route('/{record}/edit')];
    }
}
