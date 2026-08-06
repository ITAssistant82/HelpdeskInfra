<?php

namespace App\Filament\Resources\SoftwareRenewalResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LicenseSeatsRelationManager extends RelationManager
{
    protected static string $relationship = 'licenseSeats';

    protected static ?string $title = 'Slot Lisensi / Anggota Teams';

    protected static ?string $recordTitleAttribute = 'email';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->schema([
            Forms\Components\TextInput::make('email')
                ->label('Email Pengguna')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('slot_name')
                ->label('Nama Slot / Pengguna')
                ->maxLength(255),
            Forms\Components\DatePicker::make('start_date')
                ->label('Mulai Aktif')
                ->required(),
            Forms\Components\DatePicker::make('end_date')
                ->label('Berakhir'),
            Forms\Components\Select::make('status')
                ->options([
                    'Active' => 'Aktif',
                    'Inactive' => 'Tidak Aktif',
                    'Expired' => 'Berakhir',
                ])
                ->default('Active')
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->label('Keterangan')
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('end_date', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('slot_name')->label('Nama Slot')->searchable()->placeholder('-'),
                Tables\Columns\TextColumn::make('start_date')->label('Mulai')->date('d/m/Y')->sortable(),
                Tables\Columns\TextColumn::make('end_date')->label('Berakhir')->date('d/m/Y')->placeholder('-')->sortable()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : ($state && $state->diffInDays(now()) <= 30 ? 'warning' : null)),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->color(fn ($state) => match ($state) {
                    'Active' => 'success', 'Expired' => 'danger', default => 'gray',
                }),
                Tables\Columns\TextColumn::make('notes')->label('Keterangan')->limit(35)->tooltip(fn ($state) => $state),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Slot')
                    ->options([
                        'Active' => 'Aktif',
                        'Inactive' => 'Tidak Aktif',
                        'Expired' => 'Berakhir',
                    ]),
                Tables\Filters\SelectFilter::make('validity')
                    ->label('Masa Berlaku')
                    ->options([
                        'active' => 'Masih Aktif',
                        'expiring_soon' => 'Berakhir ≤ 30 Hari',
                        'expired' => 'Sudah Berakhir',
                        'no_end_date' => 'Tanpa Tanggal Berakhir',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'active' => $query->where(function (Builder $query) {
                                $query->whereNull('end_date')
                                    ->orWhereDate('end_date', '>=', today());
                            }),
                            'expiring_soon' => $query->whereDate('end_date', '>=', today())
                                ->whereDate('end_date', '<=', today()->addDays(30)),
                            'expired' => $query->whereDate('end_date', '<', today()),
                            'no_end_date' => $query->whereNull('end_date'),
                            default => $query,
                        };
                    }),
            ])
            ->headerActions([Actions\CreateAction::make()->label('Tambah Slot')])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()]);
    }
}
