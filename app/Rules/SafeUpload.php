<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SafeUpload implements ValidationRule
{
    public const IMAGE_MIME_TYPES = ['image/jpeg', 'image/png'];

    public function __construct(private bool $allowImport = false) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            $fail('File upload tidak valid.');
            return;
        }

        $name = strtolower($value->getClientOriginalName());
        // Reject dangerous suffixes even inside double extensions (shell.php.jpg).
        if (preg_match('/(?:^|\.)(?:php\d*|phtml|phar|shtml|htaccess)(?:\.|$)/i', $name)) {
            $fail('Ekstensi file tersebut tidak diizinkan.');
            return;
        }

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $types = ['jpg' => IMAGETYPE_JPEG, 'jpeg' => IMAGETYPE_JPEG, 'png' => IMAGETYPE_PNG];
        if (isset($types[$extension])) {
            $image = @getimagesize($value->getPathname());
            if ($image && $image[2] === $types[$extension] && in_array($value->getMimeType(), self::IMAGE_MIME_TYPES, true)) {
                return;
            }
        }

        if ($this->allowImport && in_array($extension, ['xls', 'xlsx', 'csv'], true)) {
            try {
                $expected = ['xls' => 'Xls', 'xlsx' => 'Xlsx', 'csv' => 'Csv'][$extension];
                if (IOFactory::identify($value->getPathname(), [$expected]) === $expected) {
                    return;
                }
            } catch (\Throwable) {
                // Invalid spreadsheet content must not pass on its extension alone.
            }
        }

        $fail($this->allowImport
            ? 'Hanya foto JPG, JPEG, PNG atau file impor Excel/CSV yang valid yang diizinkan.'
            : 'Hanya foto JPG, JPEG, atau PNG yang valid yang diizinkan.');
    }
}
