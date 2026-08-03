<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SoftwareRenewalResource\Pages;
use App\Models\SoftwareRenewal;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SoftwareRenewalResource extends Resource
{
    protected static ?string $model = SoftwareRenewal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationLabel = 'Software';

    protected static string|\UnitEnum|null $navigationGroup = 'Renewal';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'software';

    public static function canViewAny(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin', 'it_infra_l1', 'it_infra_l2', 'it_infra_l3']) ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->schema([
            Schemas\Components\Section::make('Informasi Software')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('software')
                        ->label('Software')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\DatePicker::make('renewal_date')
                        ->label('Renewal Date')
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('pricing')
                        ->label('Pricing')
                        ->placeholder('contoh: $100/tahun, Rp 1.500.000/tahun'),
                    Forms\Components\TextInput::make('email_registered')
                        ->label('Email Registered')
                        ->email()
                        ->placeholder('email untuk registrasi license'),
                    Forms\Components\TextInput::make('pic')
                        ->label('PIC')
                        ->placeholder('Person In Charge'),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'In Budget' => 'In Budget',
                            'Ex Budget' => 'Ex Budget',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('keterangan')
                        ->label('Keterangan')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('renewal_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('software')
                    ->label('Software')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('renewal_date')
                    ->label('Renewal Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : ($state && $state->diffInDays(now()) <= 30 ? 'warning' : null)),
                Tables\Columns\TextColumn::make('pricing')
                    ->label('Pricing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_registered')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('pic')
                    ->label('PIC')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color(fn ($state) => match ($state) {
                        'In Budget' => 'info',
                        'Ex Budget' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_computed')
                    ->label('Status')
                    ->options([
                        'In Budget' => 'In Budget',
                        'Ex Budget' => 'Ex Budget',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSoftwareRenewals::route('/'),
            'create' => Pages\CreateSoftwareRenewal::route('/create'),
            'edit' => Pages\EditSoftwareRenewal::route('/{record}/edit'),
        ];
    }
}
