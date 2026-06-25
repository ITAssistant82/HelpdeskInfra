<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuideResource\Pages;
use App\Models\Guide;
use Filament\Forms;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Columns\Layout\View as LayoutView;
use Filament\Tables\Table;

class GuideResource extends Resource
{
    protected static ?string $model = Guide::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Panduan';
    protected static string|\UnitEnum|null $navigationGroup = 'Ticketing';
    protected static ?int $navigationSort = 3;
    protected static ?string $recordTitleAttribute = 'title';
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->isStaff() || $user->hasRole('user'));
    }
    public static function canViewAny(): bool
    {
        return static::canAccess();
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('create_guide') ?? false;
    }
    public static function canEdit($record): bool
    {
        return auth()->user()?->can('view_any_guide') ?? false;
    }
    public static function canDelete($record): bool
    {
        return auth()->user()?->can('view_any_guide') ?? false;
    }
    public static function canView($record): bool
    {
        return static::canAccess();
    }
    public static function getCreateAuthorizationResponse(): Response
    {
        return static::canCreate() ? Response::allow() : Response::deny();
    }
    public static function getEditAuthorizationResponse(Model $record): Response
    {
        return static::canEdit($record) ? Response::allow() : Response::deny();
    }
    public static function getDeleteAuthorizationResponse(Model $record): Response
    {
        return static::canDelete($record) ? Response::allow() : Response::deny();
    }
    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->schema([Forms\Components\TextInput::make('title')->required(), Forms\Components\Select::make('category')->options(['Incident' => 'Incident', 'Service Request' => 'Service Request', 'Hardware' => 'Hardware', 'Software' => 'Software', 'Network' => 'Network', 'Account' => 'Account', 'General' => 'General', ])->nullable(), Forms\Components\RichEditor::make('content')->required()->columnSpanFull(), Forms\Components\FileUpload::make('attachments')->multiple()->storeFileNamesIn('attachment_names')->directory('guides')->maxSize(20480)->columnSpanFull(), Forms\Components\TextInput::make('order')->numeric()->default(0), Forms\Components\Toggle::make('is_active')->default(true), ]);
    }
    public static function table(Table $table): Table
    {
        $isStaff = auth()->user()?->isStaff() ?? false;
        $categories = ['Incident', 'Service Request', 'Hardware', 'Software', 'Network', 'Account', 'General'];

        return $table
            ->contentGrid(['md' => 2, 'lg' => 3])
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->hidden(),
                Tables\Columns\TextColumn::make('category')->searchable()->hidden(),
                LayoutView::make('filament.resources.guide-resource.components.guide-card'),
            ])
            ->defaultSort('order')
            ->defaultPaginationPageOption(12)
            ->paginated([12, 24, 48, 'all'])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(array_combine($categories, $categories))
                    ->placeholder('Semua Kategori'),
            ])
            ->actions([
                Actions\Action::make('view')
                    ->label('Baca')
                    ->button()
                    ->size('sm')
                    ->color('primary')
                    ->url(fn (Guide $record): string => static::getUrl('view', ['record' => $record])),
                Actions\EditAction::make()->button()->size('sm')->visible($isStaff),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()->visible($isStaff),
                ]),
            ])
            ->recordUrl(null);
    }
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return ['index' => Pages\ListGuides::route('/'), 'create' => Pages\CreateGuide::route('/create'), 'view' => Pages\ViewGuide::route('/{record}'), 'edit' => Pages\EditGuide::route('/{record}/edit'), ];
    }
}
