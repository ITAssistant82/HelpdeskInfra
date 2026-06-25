<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';
    protected static ?string $recordTitleAttribute = 'file_name';
    public function isReadOnly(): bool
    {
        return! auth()->user()->isStaff();
    }
    public function form(Schema $schema): Schema
    {
        return $schema->schema([Forms\Components\FileUpload::make('file_path')->label('File')->disk('public')->directory('ticket-attachments')->preserveFilenames()->required()->columnSpanFull(), ]);
    }
    public function table(Table $table): Table
    {
        return $table->columns([Tables\Columns\ImageColumn::make('file_path')->label('Preview')->disk('public')->square()->size(60)->visible(fn ($record) => $record && str_starts_with($record->mime_type, 'image/')), Tables\Columns\TextColumn::make('file_name')->label('Nama File')->searchable(), Tables\Columns\TextColumn::make('user.name')->label('Upload Oleh'), Tables\Columns\TextColumn::make('file_size')->label('Ukuran')->formatStateUsing(fn ($state) => $state ? round($state / 1024, 1). ' KB' : '-'), Tables\Columns\TextColumn::make('created_at')->label('Diupload')->dateTime('d/m/Y H:i'), ])->defaultSort('created_at', 'desc')->headerActions([Actions\CreateAction::make()->label('Upload File')->mutateFormDataUsing(function (array $data) {
            $data ['user_id'] = auth()->id();
            $data ['file_name'] = basename($data ['file_path']);
            $fullPath = Storage::disk('public')->path($data ['file_path']);
            if (file_exists($fullPath)) {
                $data ['file_size'] = filesize($fullPath);
                $data ['mime_type'] = mime_content_type($fullPath);
            }
            return $data;
        }), ])->actions([Actions\Action::make('view')->label('Lihat')->icon('heroicon-o-eye')->url(fn ($record) => Storage::disk('public')->url($record->file_path))->openUrlInNewTab()->visible(fn ($record) => $record && str_starts_with($record->mime_type, 'image/')), Actions\Action::make('download')->label('Download')->icon('heroicon-o-arrow-down-tray')->url(fn ($record) => Storage::disk('public')->url($record->file_path))->openUrlInNewTab(), Actions\DeleteAction::make()->visible(fn () => auth()->user()?->isStaff() ?? false)->after(function ($record) {
            Storage::disk('public')->delete($record->file_path);
        }), ]);
    }
}
