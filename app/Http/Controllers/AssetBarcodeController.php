<?php
namespace App\Http\Controllers;

use App\Models\EmployeeAsset;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AssetBarcodeController extends Controller
{
    private function generateQRCode($data)
    {
        // Keep raster size deterministic so printed QR dimensions look consistent.
        $qrCode = new QrCode(
            data: $data,
            size: 900,
            margin: 0,
            roundBlockSizeMode: RoundBlockSizeMode::None
        );
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        return base64_encode($result->getString());
    }

    private function generateQRCodeForScan(EmployeeAsset $asset)
    {
        $lines = [
            strtoupper('=== IT ASSET ==='),
            'Code: ' . $asset->asset_code,
            'Type: ' . $asset->asset_type,
            'Brand: ' . ($asset->brand ?? '-'),
            'Model: ' . ($asset->model ?? '-'),
            'S/N: ' . ($asset->serial_number ?? '-'),
        ];

        if ($asset->employee) {
            $lines[] = 'Owner: ' . $asset->employee->nik . ' - ' . $asset->employee->full_name;
            if ($asset->employee->prodi_unit_kerja) {
                $lines[] = 'Unit: ' . $asset->employee->prodi_unit_kerja;
            }
        }

        $specs = [];
        if ($asset->os) $specs[] = $asset->os;
        if ($asset->processor) $specs[] = $asset->processor;
        if ($asset->memory_gb) $specs[] = $asset->memory_gb . 'GB';
        if ($asset->hard_drive_gb) $specs[] = $asset->hard_drive_gb . 'GB';
        if ($specs) {
            $lines[] = 'Specs: ' . implode(' | ', $specs);
        }

        if ($asset->tahun_pembelian) {
            $lines[] = 'Year: ' . $asset->tahun_pembelian;
        }

        $lines[] = 'Cond: ' . ($asset->condition ?? '-');
        $lines[] = '';
        $lines[] = 'Scan by: IT Infrastructure';

        return $this->generateQRCode(implode("\n", $lines));
    }

    public function scanView(EmployeeAsset $asset)
    {
        $asset->load('employee');

        if (!$asset->employee) {
            return view('filament.resources.employee-asset-resource.scan-detail', [
                'asset' => $asset,
                'error' => 'Asset ini belum memiliki data pemilik.',
            ]);
        }

        return view('filament.resources.employee-asset-resource.scan-detail', [
            'asset' => $asset,
            'error' => null,
        ]);
    }

    public function printBarcode(EmployeeAsset $asset)
    {
        $barcode = $this->generateQRCodeForScan($asset);

        $pdf = Pdf::loadView('filament.resources.employee-asset-resource.barcode-pdf', [
            'asset' => $asset,
            'barcode' => $barcode,
        ]);

        return $pdf->download("barcode-{$asset->asset_code}.pdf");
    }

    public function previewBarcode(EmployeeAsset $asset)
    {
        $barcode = $this->generateQRCodeForScan($asset);

        return view('filament.resources.employee-asset-resource.barcode-preview', [
            'asset' => $asset,
            'barcode' => $barcode,
        ]);
    }

    public function stickerBarcode(EmployeeAsset $asset)
    {
        $barcode = $this->generateQRCodeForScan($asset);

        $pdf = Pdf::loadView('filament.resources.employee-asset-resource.sticker-pdf', [
            'asset' => $asset,
            'barcode' => $barcode,
        ])->setPaper('a4', 'portrait')
          ->setOptions([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled' => true,
              'dpi' => 96,
          ]);

        return $pdf->download("sticker-{$asset->asset_code}.pdf");
    }

    public function printMultipleBarcode()
    {
        $assets = EmployeeAsset::all();
        
        $barcodes = $assets->map(function ($asset) {
            return [
                'asset' => $asset,
                'barcode' => $this->generateQRCodeForScan($asset),
            ];
        });

        $pdf = Pdf::loadView('filament.resources.employee-asset-resource.barcode-bulk-pdf', [
            'barcodes' => $barcodes,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('barcodes-all.pdf');
    }

    public function printMultipleSticker()
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        
        // Ensure FPDF and FPDI are loaded
        require_once base_path('vendor/setasign/fpdf/fpdf.php');
        require_once base_path('vendor/setasign/fpdi/src/autoload.php');
        
        $assets = EmployeeAsset::with('employee')->get();
        $totalAssets = $assets->count();
        Log::info("printMultipleSticker: Total assets = {$totalAssets}");
        
        $barCodeStyle = '<style>
            @page { margin: 0; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            html, body { width: 210mm; height: 297mm; margin: 0; padding: 0; background: white; }
            body { font-family: "Segoe UI", Arial, sans-serif; font-size: 6pt; position: relative; }
            .label { width: 92mm; height: 45mm; padding: 1.5mm; text-align: center; border: 0.5px solid #d1d5db; position: absolute; }
            .bar { background: #1e3a5f; color: white; padding: 1mm; margin: -1.5mm -1.5mm 1mm -1.5mm; }
            .bar .title { font-size: 6pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
            .bar .sub { font-size: 4.5pt; opacity: 0.8; }
            .bar .code { display: inline-block; background: rgba(255,255,255,0.15); padding: 0.3mm 1.5mm; font-family: "Courier New", monospace; font-size: 5.5pt; font-weight: bold; margin-top: 0.5mm; }
            .qr { display: flex; flex-direction: column; align-items: center; justify-content: center; }
            .qr img { width: 30mm; height: 30mm; display: block; }
            .qr .hint { font-size: 3.5pt; color: #9ca3af; text-transform: uppercase; margin-top: 0.2mm; }
            .foot { border-top: 0.5px solid #d1d5db; padding-top: 0.5mm; margin-top: 0.5mm; font-size: 5pt; color: #374151; line-height: 1.2; }
            .foot .nik { font-size: 4.5pt; color: #6b7280; }
        </style>';

        $tempDir = storage_path('app/temp');
        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0777, true);
        }

        $pages = collect($assets)->chunk(10);
        $totalPages = $pages->count();
        Log::info("printMultipleSticker: Total pages = {$totalPages}");
        
        $pageFiles = [];

        $colW = 92;
        $colGap = 6;
        $rowH = 45;
        $rowGap = 3;
        $startX = 10;
        $startY = 30.5;

        foreach ($pages as $pi => $page) {
            $pageItems = $page->count();
            Log::info("printMultipleSticker: Page {$pi} has {$pageItems} items");
            
            $html = '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Stiker</title>' . $barCodeStyle . '</head><body>';
            
            for ($r = 0; $r < 5; $r++) {
                for ($c = 0; $c < 2; $c++) {
                    $idx = $r * 2 + $c;
                    $x = $startX + $c * ($colW + $colGap);
                    $y = $startY + $r * ($rowH + $rowGap);

                    $html .= '<div class="label" style="left: ' . $x . 'mm; top: ' . $y . 'mm;">';
                    if ($idx < $pageItems) {
                        $a = $page->values()->get($idx);
                        $barcode = $this->generateQRCode($this->buildQRContent($a));
                        $code = htmlspecialchars($a->asset_code, ENT_QUOTES);
                        // $brand = htmlspecialchars($a->brand ?? '-', ENT_QUOTES);
                        // $model = htmlspecialchars($a->model ?? '-', ENT_QUOTES);
                        // $sn = $a->serial_number ? ' | S/N: ' . htmlspecialchars($a->serial_number, ENT_QUOTES) : '';
                        // $nik = htmlspecialchars($a->employee?->nik ?? '-', ENT_QUOTES);
                        // $name = htmlspecialchars($a->employee?->full_name ?? '-', ENT_QUOTES);
                        
                        $html .= '<div class="bar"><div class="title">IT Infrastructure</div><div class="sub">Asset Management</div><div class="code">' . $code . '</div></div>';
                        $html .= '<div class="qr"><img src="data:image/png;base64,' . $barcode . '" alt="QR"><div class="hint">Scan QR</div></div>';
                        // $html .= '<div class="foot">' . $brand . ' ' . $model . $sn . '<div class="nik">' . $nik . ' - ' . $name . '</div></div>';
                    }
                    $html .= '</div>';
                }
            }
            
            $html .= '</body></html>';

            try {
                $pdf = Pdf::loadHTML($html)
                    ->setPaper('a4', 'portrait')
                    ->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true,
                        'dpi' => 96,
                    ]);

                $filename = $tempDir . '/page_' . str_pad($pi, 3, '0', STR_PAD_LEFT) . '.pdf';
                $output = $pdf->output();
                Log::info("printMultipleSticker: Page {$pi} PDF size = " . strlen($output) . " bytes");
                file_put_contents($filename, $output);
                $pageFiles[] = $filename;
            } catch (\Exception $e) {
                Log::error("printMultipleSticker: Page {$pi} error: " . $e->getMessage());
                throw $e;
            }
        }

        // Merge all page PDFs using FPDI
        $pdf = new \setasign\Fpdi\Fpdi('P', 'mm', 'A4');
        
        foreach ($pageFiles as $file) {
            if (!File::exists($file)) {
                Log::error("printMultipleSticker: File not found: {$file}");
                continue;
            }
            $pageCount = $pdf->setSourceFile($file);
            Log::info("printMultipleSticker: Merging {$file} ({$pageCount} pages)");
            for ($i = 1; $i <= $pageCount; $i++) {
                $tplIdx = $pdf->importPage($i);
                $pdf->AddPage();
                $pdf->useTemplate($tplIdx);
            }
            File::delete($file);
        }

        $pdfContent = $pdf->Output('S');
        Log::info("printMultipleSticker: Final PDF size = " . strlen($pdfContent) . " bytes");
        
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="stickers-all.pdf"',
        ]);
    }

    public function printMultipleStickerWord()
    {
        // Increase memory limit
        ini_set('memory_limit', '1024M');
        set_time_limit(300);
        
        $assets = EmployeeAsset::with('employee')->get();
        
        if ($assets->isEmpty()) {
            abort(404, 'Tidak ada asset untuk dicetak');
        }

        $tempDir = storage_path('app/temp');
        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0777, true);
        }

        $qrFiles = [];
        
        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();
            $phpWord->setDefaultFontName('Calibri');
            $phpWord->setDefaultFontSize(11);
            
            $section = $phpWord->addSection([
                'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(8.5),
                'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(11),
                'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1),
                'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1),
                'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1),
                'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1),
            ]);

            foreach ($assets as $index => $asset) {
                try {
                    // Reuse the same QR generator settings used by PDF sticker output.
                    $qrImage = base64_decode($this->generateQRCode($this->buildQRContent($asset)));
                    
                    // Save QR to temp file with absolute path
                    $qrFileName = 'qr_' . uniqid() . '_' . $asset->id . '.png';
                    $tempQrPath = $tempDir . '/' . $qrFileName;
                    
                    if (!File::put($tempQrPath, $qrImage)) {
                        throw new \Exception('Failed to write QR file');
                    }
                    
                    // Only add to array if file was successfully created
                    if (File::exists($tempQrPath)) {
                        $qrFiles[] = $tempQrPath;
                    } else {
                        throw new \Exception('QR file was not created');
                    }
                    
                    // Create table
                    $table = $section->addTable([
                        'borderSize' => 6,
                        'borderColor' => 'CCCCCC',
                        'cellMargin' => 100,
                    ]);
                    
                    // Row 1: Title
                    $row = $table->addRow(400);
                    $row->addCell(9000, ['bgColor' => '1E3A5F'])->addText(
                        'IT ASSET STICKER',
                        ['bold' => true, 'size' => 14, 'color' => 'FFFFFF']
                    );
                    
                    // Row 2: Asset Code
                    $row = $table->addRow(300);
                    $row->addCell(9000)->addText(
                        'Kode: ' . $asset->asset_code,
                        ['bold' => true, 'size' => 11]
                    );
                    
                    // Row 3: QR Code (centered)
                    $row = $table->addRow(600);
                    $cell = $row->addCell(9000, ['align' => 'center']);
                    
                    $cell->addImage($tempQrPath, [
                        'width' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
                        'height' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
                    ]);
                    
                    // Row 4: Device Info
                    $row = $table->addRow();
                    $cell = $row->addCell(9000);
                    $cell->addText('Tipe: ' . $asset->asset_type, ['size' => 10]);
                    $cell->addText('Brand: ' . ($asset->brand ?? '-'), ['size' => 10]);
                    $cell->addText('Model: ' . ($asset->model ?? '-'), ['size' => 10]);
                    if ($asset->serial_number) {
                        $cell->addText('S/N: ' . $asset->serial_number, ['size' => 10]);
                    }
                    
                    // Row 5: Owner Info
                    $row = $table->addRow();
                    $cell = $row->addCell(9000);
                    $cell->addText('Pemilik: ' . ($asset->employee?->full_name ?? '-'), ['size' => 10]);
                    $cell->addText('NIK: ' . ($asset->employee?->nik ?? '-'), ['size' => 10]);
                    if ($asset->employee?->prodi_unit_kerja) {
                        $cell->addText('Unit: ' . $asset->employee->prodi_unit_kerja, ['size' => 10]);
                    }
                    
                    // Row 6: Status
                    $row = $table->addRow();
                    $cell = $row->addCell(9000);
                    $cell->addText('Kondisi: ' . ($asset->condition ?? '-'), ['size' => 9]);
                    
                    // Page break except last
                    if ($index < $assets->count() - 1) {
                        $section->addPageBreak();
                    }
                    
                } catch (\Exception $itemErr) {
                    Log::error('Error processing asset ' . ($asset->id ?? 'unknown') . ': ' . $itemErr->getMessage());
                    continue;
                }
            }
            
            // Save document
            $filename = 'stickers-all-' . date('YmdHis') . '.docx';
            $outputPath = $tempDir . '/' . $filename;
            
            Log::info('Saving document to: ' . $outputPath);
            
            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($outputPath);
            
            // Verify file exists and has content
            if (!File::exists($outputPath) || File::size($outputPath) === 0) {
                throw new \Exception('Document failed to save or is empty. Path: ' . $outputPath);
            }
            
            Log::info('Document saved successfully. Size: ' . File::size($outputPath) . ' bytes');
            
            // Download file
            $response = response()->download($outputPath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);
            
            // Cleanup after response is sent
            $response->deleteFileAfterSend(true);
            
            // Cleanup QR files in a deferred manner (Laravel will clean up later)
            foreach ($qrFiles as $file) {
                try {
                    File::delete($file);
                } catch (\Exception $e) {
                    Log::warning('Could not delete QR file: ' . $file);
                }
            }
            
            return $response;
            
        } catch (\Exception $e) {
            // Cleanup on error
            foreach ($qrFiles as $file) {
                try {
                    if (File::exists($file)) {
                        File::delete($file);
                    }
                } catch (\Exception $cleanupErr) {
                    Log::warning('Cleanup error: ' . $cleanupErr->getMessage());
                }
            }
            
            Log::error('Cetak Stiker Word Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            abort(500, 'Error: ' . $e->getMessage());
        }
    }

    private function buildQRContent(EmployeeAsset $asset)
    {
        $lines = [
            strtoupper('=== IT ASSET ==='),
            'Code: ' . $asset->asset_code,
            'Type: ' . $asset->asset_type,
            'Brand: ' . ($asset->brand ?? '-'),
            'Model: ' . ($asset->model ?? '-'),
            'S/N: ' . ($asset->serial_number ?? '-'),
        ];

        if ($asset->employee) {
            $lines[] = 'Owner: ' . $asset->employee->nik . ' - ' . $asset->employee->full_name;
            if ($asset->employee->prodi_unit_kerja) {
                $lines[] = 'Unit: ' . $asset->employee->prodi_unit_kerja;
            }
        }

        $specs = [];
        if ($asset->os) $specs[] = $asset->os;
        if ($asset->processor) $specs[] = $asset->processor;
        if ($asset->memory_gb) $specs[] = $asset->memory_gb . 'GB';
        if ($asset->hard_drive_gb) $specs[] = $asset->hard_drive_gb . 'GB';
        if ($specs) {
            $lines[] = 'Specs: ' . implode(' | ', $specs);
        }

        if ($asset->tahun_pembelian) {
            $lines[] = 'Year: ' . $asset->tahun_pembelian;
        }

        $lines[] = 'Cond: ' . ($asset->condition ?? '-');
        $lines[] = '';
        $lines[] = 'Scan by: IT Infrastructure';

        return implode("\n", $lines);
    }
}
