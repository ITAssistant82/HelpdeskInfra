<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 4;
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('view_any_user'));
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('create_user'));
    }
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('view_any_user'));
    }
    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('view_any_user'));
    }
    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->schema([Forms\Components\TextInput::make('name')->label('Nama')->required(), Forms\Components\TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true), Forms\Components\TextInput::make('password')->label('Password')->password()->required(fn (string $operation): bool => $operation === 'create')->visible(fn (string $operation): bool => $operation === 'create'), Forms\Components\TextInput::make('new_password')->label('Password Baru')->password()->visible(fn (string $operation): bool => $operation === 'edit')->nullable(), Forms\Components\TextInput::make('jabatan')->label('Jabatan')->nullable(), Forms\Components\TextInput::make('unit')->label('Unit')->nullable(), Forms\Components\TextInput::make('no_hp')->label('No. HP')->nullable(), Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true), Forms\Components\Select::make('roles')->label('Roles')->multiple()->relationship('roles', 'name')->preload()->required(), ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(), Tables\Columns\TextColumn::make('email')->label('Email')->searchable(), Tables\Columns\TextColumn::make('jabatan')->label('Jabatan')->searchable()->toggleable(), Tables\Columns\TextColumn::make('unit')->label('Unit')->searchable()->toggleable(), Tables\Columns\TextColumn::make('roles.name')->label('Roles')->badge()->color('info'), Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(), Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime(), ])->filters([])->actions([Actions\EditAction::make(), Actions\DeleteAction::make(), ])->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make(), ]), ]);
    }
    public static function getPages(): array
    {
        return ['index' => Pages\ListUsers::route('/'), 'create' => Pages\CreateUser::route('/create'), 'edit' => Pages\EditUser::route('/{record}/edit'), ];
    }
}
