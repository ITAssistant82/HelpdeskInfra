<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Observers\TicketObserver;
use App\Rules\SafeUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Ticket::observe(TicketObserver::class);

        // Validate before Livewire writes any temporary upload, including editor images.
        Validator::extend('safe_temporary_upload', function ($attribute, $value): bool {
            return Validator::make(['file' => $value], ['file' => [new SafeUpload(allowImport: true)]])->passes();
        }, 'File ditolak. Gunakan foto JPG, JPEG, PNG atau file impor Excel/CSV yang valid; ekstensi executable tidak diizinkan.');
        config(['livewire.temporary_file_upload.rules' => ['required', 'file', 'max:12288', 'safe_temporary_upload']]);

        FileUpload::configureUsing(function (FileUpload $upload): void {
            $upload->acceptedFileTypes(SafeUpload::IMAGE_MIME_TYPES);
            $upload->rule(fn (FileUpload $component) => new SafeUpload(
                allowImport: in_array($component->getDirectory(), ['imports', 'ticket-imports'], true),
            ));
        });

        RichEditor::configureUsing(function (RichEditor $editor): void {
            $editor->fileAttachmentsAcceptedFileTypes(SafeUpload::IMAGE_MIME_TYPES);
            $editor->saveUploadedFileAttachmentUsing(function ($file, RichEditor $component) {
                Validator::make(['file' => $file], ['file' => [new SafeUpload]])->validate();

                return $component->defaultSaveUploadedFileAttachment($file)
                    ?? $file->store($component->getFileAttachmentsDirectory(), $component->getFileAttachmentsDiskName());
            });
        });
    }
}
