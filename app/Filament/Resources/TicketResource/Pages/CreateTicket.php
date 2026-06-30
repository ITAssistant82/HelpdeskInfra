<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
    protected array $uploadedFiles = [];
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data ['requester_id'] = auth()->id();
        $data ['status'] = 'New';
        $data ['impact'] ??= 'Medium';

        if (empty($data['assigned_group']) && !empty($data['category_id'])) {
            $category = \App\Models\TicketCategory::find($data['category_id']);
            if ($category && $category->assigned_team) {
                $data['assigned_group'] = $category->assigned_team;
            }
        }
        if (!empty($data['assigned_group']) && empty($data['team_key'])) {
            $layer = \App\Models\TicketLayer::where('role_name', $data['assigned_group'])->first();
            if ($layer && $layer->team_key) {
                $data['team_key'] = $layer->team_key;
            }
        }
        if (isset($data ['new_attachments']) && is_array($data ['new_attachments'])) {
            $this->uploadedFiles = $data ['new_attachments'];
        }
        unset($data ['new_attachments']);
        return $data;
    }
    protected function afterCreate(): void
    {
        foreach ($this->uploadedFiles as $path) {
            $fullPath = Storage::disk('public')->path($path);
            $this->record->attachments()->create(['user_id' => auth()->id(), 'file_path' => $path, 'file_name' => basename($path), 'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0, 'mime_type' => file_exists($fullPath) ? mime_content_type($fullPath) : 'application/octet-stream', ]);
        }

        if ($this->record->isOutsideWorkingHours()) {
            Notification::make()
                ->warning()
                ->title('Tiket di Luar Jam Kerja')
                ->body('Tiket Anda akan diproses pada jam kerja Senin-Jumat 08:00-17:00.')
                ->send();
        }
    }
    protected function getRedirectUrl(): string
    {
        return TicketResource::getUrl('index');
    }
}
