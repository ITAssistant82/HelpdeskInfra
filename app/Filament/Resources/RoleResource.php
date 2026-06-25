<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Spatie\Permission\Models\Role;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Roles';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('view_any_role'));
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('create_role'));
    }
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('view_any_role'));
    }
    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('view_any_role'));
    }
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\TextInput::make('name')->label('Nama Role')->required()->unique(ignoreRecord: true), Forms\Components\TextInput::make('guard_name')->label('Guard')->default('web')->required(), Select::make('permissions')->label('Permissions')->multiple()->relationship('permissions', 'name')->preload(), ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('name')->label('Role')->searchable(),             Tables\Columns\TextColumn::make('guard_name')->label('Guard'), Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime(), ])->filters([])->actions([Actions\EditAction::make(), Actions\DeleteAction::make(), ])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make(), ]), ]);
    }
    public static function getPages(): array
    {
        return ['index' => Pages\ListRoles::route('/'), 'create' => Pages\CreateRole::route('/create'), 'edit' => Pages\EditRole::route('/{record}/edit'), ];
    }
}
