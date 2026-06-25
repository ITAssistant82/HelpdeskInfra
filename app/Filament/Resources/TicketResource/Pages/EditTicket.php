<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;
    protected array $uploadedFiles = [];
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data ['new_attachments']) && is_array($data ['new_attachments'])) {
            $this->uploadedFiles = $data ['new_attachments'];
        }
        unset($data ['new_attachments']);
        return $data;
    }
    protected function afterSave(): void
    {
        foreach ($this->uploadedFiles as $path) {
            $fullPath = Storage::disk('public')->path($path);
            $this->record->attachments()->create(['user_id' => auth()->id(), 'file_path' => $path, 'file_name' => basename($path), 'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0, 'mime_type' => file_exists($fullPath) ? mime_content_type($fullPath) : 'application/octet-stream', ]);
        }
    }
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make(), Actions\ForceDeleteAction::make(), Actions\RestoreAction::make(), ];
    }
}
