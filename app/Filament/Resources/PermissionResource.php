<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Permissions';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = true;

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasRole('super_admin') || $user->can('view_any_permission'));
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasRole('super_admin') || $user->can('create_permission'));
    }

    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();

        return $user && ($user->hasRole('super_admin') || $user->can('view_any_permission'));
    }

    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();

        return $user && ($user->hasRole('super_admin') || $user->can('view_any_permission'));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\TextInput::make('name')->label('Nama Permission')->required()->unique(ignoreRecord: true), Forms\Components\TextInput::make('guard_name')->label('Guard')->default('web')->required()]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('name')->label('Permission')->searchable(), Tables\Columns\TextColumn::make('guard_name')->label('Guard'), Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime()])->filters([])->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListPermissions::route('/'), 'create' => Pages\CreatePermission::route('/create'), 'edit' => Pages\EditPermission::route('/{record}/edit')];
    }
}
