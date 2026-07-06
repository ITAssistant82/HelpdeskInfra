<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Activity Log';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        $user = Auth::user();

        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_activity_log'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(EloquentModel $record): bool
    {
        return false;
    }

    public static function canDelete(EloquentModel $record): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\TextInput::make('description')->label('Deskripsi'), Forms\Components\TextInput::make('log_name')->label('Log Name'), Forms\Components\TextInput::make('subject_type')->label('Subject')->formatStateUsing(fn ($state) => class_basename($state)), Forms\Components\KeyValue::make('properties')->label('Data Perubahan')]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([Tables\Columns\TextColumn::make('log_name')->label('Log')->badge()->colors(['info' => 'default']), Tables\Columns\TextColumn::make('description')->label('Aktivitas')->searchable()->limit(50), Tables\Columns\TextColumn::make('subject_type')->label('Model')->formatStateUsing(fn ($state) => class_basename($state))->badge()->color('gray'), Tables\Columns\TextColumn::make('causer_id')->label('User')->formatStateUsing(fn ($record) => $record->causer?->name ?? $record->causer?->email ?? '-'), Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i:s')->sortable()])->filters([Tables\Filters\SelectFilter::make('log_name')->label('Jenis Log')->options(fn () => Activity::distinct()->pluck('log_name', 'log_name')->toArray()), Filter::make('causer')->form([Select::make('causer_id')->label('User')->searchable()->options(User::all()->pluck('name', 'id')->toArray())])->query(function ($query, array $data) {
            return $query->when($data['causer_id'], fn ($q) => $q->where('causer_id', $data['causer_id'])->where('causer_type', User::class));
        }), ])->actions([Actions\ViewAction::make()->modalHeading('Detail Activity Log')->form([Forms\Components\TextInput::make('description')->label('Deskripsi'), Forms\Components\TextInput::make('log_name')->label('Log Name'), Forms\Components\TextInput::make('subject_type')->label('Subject')->formatStateUsing(fn ($state) => class_basename($state)), Forms\Components\TextInput::make('causer_id')->label('Dilakukan oleh')->formatStateUsing(fn ($record) => $record->causer?->name ?? $record->causer?->email ?? '-'), Forms\Components\KeyValue::make('properties')->label('Data Perubahan')])])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListActivityLogs::route('/')];
    }
}
