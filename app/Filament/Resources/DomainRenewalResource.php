<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DomainRenewalResource\Pages;
use App\Models\DomainRenewal;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DomainRenewalResource extends Resource
{
    protected static ?string $model = DomainRenewal::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Domain';

    protected static string|\UnitEnum|null $navigationGroup = 'Renewal';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'domain';

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
            Schemas\Components\Section::make('Informasi Domain')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('domain')
                        ->label('Domain')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\DatePicker::make('registration_date')
                        ->label('Tanggal Pendaftaran'),
                    Forms\Components\DatePicker::make('expiration_date')
                        ->label('Tanggal Jatuh Tempo')
                        ->required()
                        ->live(),
                    Forms\Components\TextInput::make('platform')
                        ->label('Platform')
                        ->placeholder('contoh: Namecheap, GoDaddy, Cloudflare'),
                    Forms\Components\Placeholder::make('auto_status')
                        ->label('Status')
                        ->content(function ($get) {
                            $expirationDate = $get('expiration_date');
                            if (! $expirationDate) {
                                return 'Active';
                            }
                            $now = \Illuminate\Support\Carbon::now();
                            $expiry = \Illuminate\Support\Carbon::parse($expirationDate);
                            if ($expiry->isPast()) {
                                return 'Expired';
                            }
                            if ($expiry->lte($now->copy()->addDays(30))) {
                                return 'Expiring Soon';
                            }
                            return 'Active';
                        })->columnSpanFull(),
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
            ->defaultSort('expiration_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('registration_date')
                    ->label('Tanggal Daftar')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiration_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : ($state && $state->diffInDays(now()) <= 30 ? 'warning' : null)),
                Tables\Columns\TextColumn::make('platform')
                    ->label('Platform')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->computeStatus())
                    ->color(fn ($state) => match ($state) {
                        'Active' => 'success',
                        'Expiring Soon' => 'warning',
                        'Expired' => 'danger',
                        'Renewed' => 'info',
                        'Cancelled' => 'gray',
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
                        'Active' => 'Active',
                        'Expiring Soon' => 'Expiring Soon',
                        'Expired' => 'Expired',
                        'Renewed' => 'Renewed',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->query(fn ($query, $state) => $query->when($state, fn ($q, $s) => match ($s) {
                        'Active' => $q->where(fn ($sub) => $sub->whereNull('status')->where('expiration_date', '>', now()->addDays(30))
                            ->orWhere('status', 'Active')),
                        'Expiring Soon' => $q->where(fn ($sub) => $sub->whereNull('status')->where('expiration_date', '<=', now()->addDays(30))->where('expiration_date', '>=', now())
                            ->orWhere('status', 'Expiring Soon')),
                        'Expired' => $q->where(fn ($sub) => $sub->whereNull('status')->where('expiration_date', '<', now())
                            ->orWhere('status', 'Expired')),
                        'Renewed' => $q->where('status', 'Renewed'),
                        'Cancelled' => $q->where('status', 'Cancelled'),
                        default => $q,
                    })),
                Tables\Filters\SelectFilter::make('platform')
                    ->options(fn () => DomainRenewal::whereNotNull('platform')->distinct()->pluck('platform', 'platform')->toArray()),
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
            'index' => Pages\ListDomainRenewals::route('/'),
            'create' => Pages\CreateDomainRenewal::route('/create'),
            'edit' => Pages\EditDomainRenewal::route('/{record}/edit'),
        ];
    }
}
