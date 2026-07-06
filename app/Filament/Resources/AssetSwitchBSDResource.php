<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetSwitchBSDResource\Pages;
use App\Models\AssetSwitch;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AssetSwitchBSDResource extends Resource
{
    protected static ?string $model = AssetSwitch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-server';

    protected static ?string $navigationLabel = 'BSD';

    protected static string|\UnitEnum|null $navigationGroup = 'Asset Switch';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_asset_switch_bsd'));
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('create_asset_switch_bsd'));
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_asset_switch_bsd'));
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_asset_switch_bsd'));
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('location', 'BSD');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->schema([Schemas\Components\Section::make('Identitas Switch')->schema([Forms\Components\TextInput::make('host_name')->label('Host Name')->required(), Forms\Components\TextInput::make('ip')->label('IP')->required(), Forms\Components\TextInput::make('brand')->label('Brand')->required(), Forms\Components\TextInput::make('type')->label('Type')->required(), Forms\Components\TextInput::make('series')->label('Series')->nullable(), Forms\Components\TextInput::make('serial_number')->label('Serial Number')->unique(ignoreRecord: true)->nullable(), Forms\Components\TextInput::make('product_name')->label('Product Name')->nullable(), Forms\Components\DatePicker::make('eol_announcement')->label('EOL Announcement')->nullable(), Forms\Components\DatePicker::make('end_of_sale')->label('End of Sale (EOS)')->nullable(), Forms\Components\DatePicker::make('end_of_service_life')->label('End of Service Life (EOSL)')->nullable()])->columns(2), Schemas\Components\Section::make('Jaringan & Koneksi')->schema([Forms\Components\TextInput::make('network_device')->label('Network Device')->nullable(), Forms\Components\TextInput::make('stacking')->label('Stacking')->nullable(), Forms\Components\TextInput::make('snmp')->label('SNMP')->nullable(), Forms\Components\TextInput::make('remote_type')->label('Remote Type')->nullable()])->columns(2), Schemas\Components\Section::make('Lokasi')->schema([Forms\Components\TextInput::make('ruangan')->label('Ruangan')->nullable(), Forms\Components\TextInput::make('tower')->label('Tower')->nullable()])->columns(2), Forms\Components\Hidden::make('location')->default('BSD'), Schemas\Components\Section::make('Uplink & Downlink')->schema([Forms\Components\TextInput::make('uplink_port')->label('Uplink Port')->nullable(), Forms\Components\TextInput::make('uplink_switch')->label('Uplink Switch')->nullable(), Forms\Components\TextInput::make('downlink_port')->label('Downlink Port')->nullable()])->columns(2), Schemas\Components\Section::make('Kredensial')->schema([Forms\Components\TextInput::make('username')->label('Username')->nullable(), Forms\Components\TextInput::make('password')->label('Password')->password()->revealable()->nullable()])->columns(2), Schemas\Components\Section::make('Lainnya')->schema([Forms\Components\Textarea::make('keterangan')->label('Keterangan')->rows(4)->columnSpanFull()->nullable()])]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('host_name')->label('Host Name')->searchable()->sortable(), Tables\Columns\TextColumn::make('ip')->label('IP')->searchable()->sortable(), Tables\Columns\TextColumn::make('network_device')->label('Network Device')->searchable()->sortable(), Tables\Columns\TextColumn::make('stacking')->label('Stacking')->searchable()->sortable(), Tables\Columns\TextColumn::make('snmp')->label('SNMP')->searchable()->sortable(), Tables\Columns\TextColumn::make('brand')->label('Brand')->searchable()->sortable(), Tables\Columns\TextColumn::make('type')->label('Type')->searchable()->sortable(), Tables\Columns\TextColumn::make('series')->label('Series')->searchable()->sortable(), Tables\Columns\TextColumn::make('remote_type')->label('Remote Type')->searchable()->sortable(), Tables\Columns\TextColumn::make('username')->label('Username')->searchable()->sortable()->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false), Tables\Columns\TextColumn::make('password')->label('Password')->sortable()->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false), Tables\Columns\TextColumn::make('ruangan')->label('Ruangan')->searchable()->sortable(), Tables\Columns\TextColumn::make('tower')->label('Tower')->searchable()->sortable(), Tables\Columns\TextColumn::make('uplink_port')->label('Uplink Port')->searchable()->sortable(), Tables\Columns\TextColumn::make('uplink_switch')->label('Uplink Switch')->searchable()->sortable(), Tables\Columns\TextColumn::make('downlink_port')->label('Downlink Port')->searchable()->sortable(), Tables\Columns\TextColumn::make('serial_number')->label('Serial Number')->searchable()->sortable(), Tables\Columns\TextColumn::make('product_name')->label('Product Name')->searchable()->sortable(), Tables\Columns\TextColumn::make('eol_announcement')->label('EOL Announcement')->date()->sortable(), Tables\Columns\TextColumn::make('end_of_sale')->label('End of Sale (EOS)')->date()->sortable(), Tables\Columns\TextColumn::make('end_of_service_life')->label('End of Service Life (EOSL)')->date()->sortable(), Tables\Columns\TextColumn::make('keterangan')->label('Keterangan')->limit(50)])->filters([Tables\Filters\SelectFilter::make('brand')->label('Brand')->options(fn () => AssetSwitch::where('location', 'BSD')->whereNotNull('brand')->distinct()->pluck('brand', 'brand')->toArray()), Tables\Filters\SelectFilter::make('type')->label('Type')->options(fn () => AssetSwitch::where('location', 'BSD')->whereNotNull('type')->distinct()->pluck('type', 'type')->toArray()), Tables\Filters\SelectFilter::make('ruangan')->label('Ruangan')->options(fn () => AssetSwitch::where('location', 'BSD')->whereNotNull('ruangan')->distinct()->pluck('ruangan', 'ruangan')->toArray()), Tables\Filters\SelectFilter::make('tower')->label('Tower')->options(fn () => AssetSwitch::where('location', 'BSD')->whereNotNull('tower')->distinct()->pluck('tower', 'tower')->toArray())])->actions([Actions\EditAction::make()])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListAssetSwitchBSDs::route('/'), 'create' => Pages\CreateAssetSwitchBSD::route('/create'), 'edit' => Pages\EditAssetSwitchBSD::route('/{record}/edit')];
    }
}
