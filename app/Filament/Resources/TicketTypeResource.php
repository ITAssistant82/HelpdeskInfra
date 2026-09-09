<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketTypeResource\Pages;
use App\Models\TicketType;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TicketTypeResource extends Resource
{
    protected static ?string $model = TicketType::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationLabel = 'Tipe Tiket';

    protected static string|\UnitEnum|null $navigationGroup = 'Ticketing';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return Auth::user()?->isITStaff() ?? false;
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
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->label('Nama Tipe')->required()->maxLength(255)->unique(ignoreRecord: true),
            Forms\Components\Select::make('color')
                ->label('Warna Badge')
                ->options(TicketType::COLORS)
                ->default(fn (): string => TicketType::suggestedColor())
                ->required(),
            Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0)->minValue(0)->required(),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            Forms\Components\Toggle::make('it_only')
                ->label('Khusus IT')
                ->helperText('Jika aktif, tipe ini tidak akan terlihat oleh user biasa.')
                ->default(false),
            Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama Tipe')->badge()->color(fn (TicketType $record): string => $record->color)->searchable()->sortable(),
            Tables\Columns\TextColumn::make('description')->label('Deskripsi')->limit(60),
            Tables\Columns\TextColumn::make('sort_order')->label('Urutan')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->label('Aktif')->boolean(),
            Tables\Columns\IconColumn::make('it_only')->label('Khusus IT')->boolean(),
        ])->actions([
            Actions\EditAction::make(),
            Actions\DeleteAction::make()
                ->before(fn (Actions\DeleteAction $action, TicketType $record) => static::preventDeletingUsedType($action, $record)),
        ]);
    }

    public static function preventDeletingUsedType(Actions\DeleteAction $action, TicketType $record): void
    {
        if (! $record->isInUse()) {
            return;
        }

        Notification::make()
            ->title('Tipe tiket tidak dapat dihapus')
            ->body('Tipe ini sudah digunakan oleh tiket atau kategori. Nonaktifkan opsi Aktif agar tidak muncul pada tiket baru.')
            ->warning()
            ->persistent()
            ->send();

        $action->halt();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTicketTypes::route('/'),
            'create' => Pages\CreateTicketType::route('/create'),
            'edit' => Pages\EditTicketType::route('/{record}/edit'),
        ];
    }
}
