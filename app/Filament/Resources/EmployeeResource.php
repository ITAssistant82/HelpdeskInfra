<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers\AssetsRelationManager;
use App\Models\Employee;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Employees';
    protected static string|\UnitEnum|null $navigationGroup = 'Employees';
    protected static ?int $navigationSort = 1;
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_employee'));
    }
    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('create_employee'));
    }
    public static function canEdit($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_employee'));
    }
    public static function canDelete($record): bool
    {
        $user = auth()->user();
        return $user && ($user->hasAnyRole(['super_admin', 'admin']) || $user->can('view_any_employee'));
    }
    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->schema([
            Schemas\Components\Section::make('Identitas Karyawan')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('nik')
                        ->label('NIK')->required()->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('inisial')
                        ->label('Inisial')->nullable(),
                    Forms\Components\TextInput::make('full_name')
                        ->label('Full Name')->required()->columnSpanFull(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')->email()->nullable()->columnSpanFull(),
                ]),
            Schemas\Components\Section::make('Informasi Kepegawaian')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('prodi_unit_kerja')
                        ->label('Prodi / Unit Kerja')->required(),
                    Forms\Components\Select::make('employee_group')
                        ->label('Employee Group')
                        ->options([
                            'Tetap' => 'Tetap',
                            'Kontrak' => 'Kontrak',
                            'Outsource' => 'Outsource',
                            'PS Full Time' => 'PS Full Time',
                            'FM Full Time' => 'FM Full Time',
                            'Lain-lain' => 'Lain-lain',
                        ])->required(),
                    Forms\Components\Select::make('work_contract')
                        ->label('Work Contract')
                        ->options([
                            'PKWT' => 'PKWT',
                            'Permanent' => 'Permanent',
                            'Purnabakti' => 'Purnabakti',
                            'Magang' => 'Magang',
                        ])->required(),
                ]),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('nik')->searchable(),
            Tables\Columns\TextColumn::make('inisial')->label('Inisial')->searchable(),
            Tables\Columns\TextColumn::make('full_name')->searchable(),
            Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
            Tables\Columns\TextColumn::make('prodi_unit_kerja')->label('Prodi / Unit Kerja')->searchable(),
            Tables\Columns\TextColumn::make('employee_group'),
            Tables\Columns\TextColumn::make('work_contract'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->filters([
            Tables\Filters\SelectFilter::make('employee_group')->label('Employee Group')->options(['Tetap' => 'Tetap', 'Kontrak' => 'Kontrak', 'Outsource' => 'Outsource', 'PS Full Time' => 'PS Full Time', 'FM Full Time' => 'FM Full Time', 'Lain-lain' => 'Lain-lain']),
            Tables\Filters\SelectFilter::make('work_contract')->label('Work Contract')->options(['PKWT' => 'PKWT', 'Permanent' => 'Permanent', 'Purnabakti' => 'Purnabakti', 'Magang' => 'Magang']),
        ])
        ->actions([
            Actions\Action::make('viewAssets')
                ->label('Assets')
                ->icon('heroicon-o-computer-desktop')
                ->color('info')
                ->modalHeading(fn (Employee $record) => 'Asset: ' . $record->full_name . ' (' . $record->nik . ')')
                ->modalWidth('7xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Tutup')
                ->modalContent(fn (Employee $record) => new \Illuminate\Support\HtmlString(
                    view('filament.resources.employee-resource.asset-modal', [
                        'employee' => $record->load('assets'),
                    ])->render()
                )),
            Actions\EditAction::make(),
        ])->bulkActions([
            Actions\BulkActionGroup::make([
                Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }
    public static function getRelations(): array
    {
        return [
            AssetsRelationManager::class,
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
