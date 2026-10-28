<?php

use App\Rules\SafeUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Livewire\Features\SupportFileUploads\FileUploadController;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

uses(Tests\TestCase::class);

it('accepts real photos and rejects executable names even with real image contents', function () {
    foreach (['jpg', 'jpeg', 'png'] as $extension) {
        $photo = UploadedFile::fake()->image('photo.'.$extension);
        expect(Validator::make(['file' => $photo], ['file' => [new SafeUpload]])->passes())->toBeTrue();
    }

    foreach (['shell.php', 'shell.phtml', 'shell.phar', 'shell.php5', 'shell.shtml', '.htaccess', 'shell.PHP', 'shell.php.jpg', '.htaccess.png'] as $name) {
        $photo = UploadedFile::fake()->image('photo.png');
        $file = new UploadedFile($photo->getPathname(), $name, 'image/png', null, true);
        expect(Validator::make(['file' => $file], ['file' => [new SafeUpload]])->fails())->toBeTrue();
    }
});

it('rejects forged MIME types, renamed scripts, SVG, PDF and mismatched photo extensions', function () {
    $webp = UploadedFile::fake()->image('photo.webp');
    expect(Validator::make(['file' => $webp], ['file' => [new SafeUpload]])->fails())->toBeTrue();
    expect(Validator::make(['file' => $webp], ['file' => config('livewire.temporary_file_upload.rules')])->fails())->toBeTrue();
    foreach (['fake.jpg', 'script.png', 'image.svg', 'document.pdf'] as $name) {
        $file = UploadedFile::fake()->createWithContent($name, '<?php echo "test"; ?>');
        expect(Validator::make(['file' => $file], ['file' => [new SafeUpload]])->fails())->toBeTrue();
    }
    $photo = UploadedFile::fake()->image('photo.png');
    $file = new UploadedFile($photo->getPathname(), 'photo.jpg', 'image/jpeg', null, true);
    expect(Validator::make(['file' => $file], ['file' => [new SafeUpload]])->fails())->toBeTrue();
});

it('allows spreadsheets at the temporary upload stage but rejects them as attachments', function () {
    $path = tempnam(sys_get_temp_dir(), 'upload-test-');
    try {
        $sheet = new Spreadsheet;
        $sheet->getActiveSheet()->setCellValue('A1', 'Asset Code');
        (new Xlsx($sheet))->save($path);
        $file = new UploadedFile($path, 'assets.xlsx', null, null, true);
        expect(Validator::make(['file' => $file], ['file' => [new SafeUpload(allowImport: true)]])->passes())->toBeTrue();
        expect(Validator::make(['file' => $file], ['file' => [new SafeUpload]])->fails())->toBeTrue();
        expect(Validator::make(['file' => $file], ['file' => config('livewire.temporary_file_upload.rules')])->passes())->toBeTrue();
    } finally {
        @unlink($path);
    }
    $fake = UploadedFile::fake()->createWithContent('fake.xlsx', '<?php echo "test"; ?>');
    expect(Validator::make(['file' => $fake], ['file' => [new SafeUpload(allowImport: true)]])->fails())->toBeTrue();
});

it('rejects an entire temporary batch before any files are stored', function () {
    Illuminate\Support\Facades\Storage::fake('local');
    $files = [UploadedFile::fake()->image('photo.jpg'), UploadedFile::fake()->createWithContent('shell.php', '<?php echo 1;')];
    expect(fn () => app(FileUploadController::class)->validateAndStore($files, 'local'))
        ->toThrow(Illuminate\Validation\ValidationException::class);
    expect(Illuminate\Support\Facades\Storage::disk('local')->allFiles())->toBeEmpty();
});

it('defaults upload fields to photo types while allowing explicit import types', function () {
    expect(FileUpload::make('attachment')->getAcceptedFileTypes())->toBe(SafeUpload::IMAGE_MIME_TYPES);
    expect(FileUpload::make('file')->directory('imports')->acceptedFileTypes(['application/vnd.ms-excel'])->getAcceptedFileTypes())
        ->toBe(['application/vnd.ms-excel']);
    expect(FileUpload::make('attachment')->shouldPreserveFilenames())->toBeFalse();
});

it('validates Livewire temporary photos and rejects spreadsheet editor attachments before saving', function () {
    Illuminate\Support\Facades\Storage::fake('tmp-for-tests');
    $controller = app(FileUploadController::class);
    $paths = $controller->validateAndStore([UploadedFile::fake()->image('photo.png')], 'tmp-for-tests');
    $photo = TemporaryUploadedFile::createFromLivewire(TemporaryUploadedFile::extractPathFromSignedPath($paths[0]));
    expect(Validator::make(['file' => $photo], ['file' => [new SafeUpload]])->passes())->toBeTrue();

    $paths = $controller->validateAndStore([UploadedFile::fake()->createWithContent('assets.csv', "code,name\nA1,Laptop\n")], 'tmp-for-tests');
    $sheet = TemporaryUploadedFile::createFromLivewire(TemporaryUploadedFile::extractPathFromSignedPath($paths[0]));
    $livewire = new class extends Livewire\Component implements Filament\Schemas\Contracts\HasSchemas {
        use Filament\Schemas\Concerns\InteractsWithSchemas;
    };
    $editor = RichEditor::make('content')->container(Filament\Schemas\Schema::make($livewire));
    expect(fn () => $editor->saveUploadedFileAttachment($sheet))
        ->toThrow(Illuminate\Validation\ValidationException::class);

    $attachment = FileUpload::make('file')->container(Filament\Schemas\Schema::make($livewire));
    expect(Validator::make(['file' => [$photo]], ['file' => $attachment->getValidationRules()])->passes())->toBeTrue();
    expect(Validator::make(['file' => [$sheet]], ['file' => $attachment->getValidationRules()])->fails())->toBeTrue();
    $import = FileUpload::make('file')->directory('ticket-imports')->acceptedFileTypes(['text/plain', 'text/csv'])
        ->container(Filament\Schemas\Schema::make($livewire));
    expect(Validator::make(['file' => [$sheet]], ['file' => $import->getValidationRules()])->passes())->toBeTrue();
});
