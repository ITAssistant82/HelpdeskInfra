<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Barcode - {{ $asset->asset_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }

        .preview-container {
            max-width: 500px;
            margin: 0 auto;
        }

        .barcode-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .card-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f59e0b;
        }

        .card-header h2 {
            font-size: 18px;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .card-header .asset-code {
            font-size: 13px;
            color: #6b7280;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .info-box {
            background: #f9fafb;
            border-radius: 6px;
            padding: 12px;
            border-left: 3px solid #f59e0b;
        }

        .info-box.full {
            grid-column: 1 / -1;
        }

        .info-box .label {
            font-size: 10px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-box .value {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
        }

        .info-box .value.mono {
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }

        .qrcode-container {
            text-align: center;
            background: #f0fdf4;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .qrcode-container img {
            max-width: 240px;
            height: auto;
        }

        .qrcode-container .scan-hint {
            font-size: 11px;
            color: #6b7280;
            margin-top: 8px;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-baik { background: #d1fae5; color: #065f46; }
        .badge-perlu { background: #fef3c7; color: #92400e; }
        .badge-rusak { background: #fee2e2; color: #991b1b; }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px 16px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: #f59e0b;
            color: white;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #1f2937;
        }

        .footer-info {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 15px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        @media (max-width: 600px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="barcode-card">
            <div class="card-header">
                <h2>{{ $asset->brand }} {{ $asset->model }}</h2>
                <div class="asset-code">{{ $asset->asset_code }}</div>
            </div>

            <div class="content-grid">
                <div class="info-box">
                    <div class="label">Asset Type</div>
                    <div class="value">{{ $asset->asset_type }}</div>
                </div>
                <div class="info-box">
                    <div class="label">Kondisi</div>
                    <div class="value">
                        <span class="badge badge-{{ str_replace(' ', '', strtolower($asset->condition)) }}">{{ $asset->condition }}</span>
                    </div>
                </div>
                @if ($asset->serial_number)
                <div class="info-box">
                    <div class="label">Serial Number</div>
                    <div class="value mono">{{ $asset->serial_number }}</div>
                </div>
                @endif
                @if ($asset->tahun_pembelian)
                <div class="info-box">
                    <div class="label">Tahun Pembelian</div>
                    <div class="value">{{ $asset->tahun_pembelian }}</div>
                </div>
                @endif
                @if ($asset->employee)
                <div class="info-box">
                    <div class="label">NIK</div>
                    <div class="value mono">{{ $asset->employee->nik }}</div>
                </div>
                <div class="info-box">
                    <div class="label">Pemilik</div>
                    <div class="value">{{ $asset->employee->full_name }}</div>
                </div>
                @if ($asset->employee->prodi_unit_kerja)
                <div class="info-box full">
                    <div class="label">Unit Kerja</div>
                    <div class="value">{{ $asset->employee->prodi_unit_kerja }}</div>
                </div>
                @endif
                @endif
                @if ($asset->os || $asset->processor || $asset->memory_gb || $asset->hard_drive_gb)
                <div class="info-box full">
                    <div class="label">Spesifikasi</div>
                    <div class="value" style="font-size:12px;">
                        {{ $asset->os ? 'OS: '.$asset->os : '' }}{{ $asset->processor ? ' | CPU: '.$asset->processor : '' }}{{ $asset->memory_gb ? ' | RAM: '.$asset->memory_gb.'GB' : '' }}{{ $asset->hard_drive_gb ? ' | Storage: '.$asset->hard_drive_gb.'GB' : '' }}
                    </div>
                </div>
                @endif
                @if ($asset->assigned_at)
                <div class="info-box">
                    <div class="label">Tanggal Diberikan</div>
                    <div class="value">{{ $asset->assigned_at->format('d M Y') }}</div>
                </div>
                @endif
                @if ($asset->notes)
                <div class="info-box full">
                    <div class="label">Catatan</div>
                    <div class="value" style="font-size:12px;">{{ $asset->notes }}</div>
                </div>
                @endif
            </div>

            <div class="qrcode-container">
                <img src="data:image/png;base64,{{ $barcode }}" alt="QR Code">
                <div class="scan-hint">Scan QR code untuk detail lengkap</div>
            </div>

            <div class="actions">
                <button class="btn btn-primary" onclick="window.print()">Cetak</button>
                <button class="btn btn-secondary" onclick="window.close()">Tutup</button>
            </div>

            <div class="footer-info">
                {{ config('app.name', 'Asset Management') }} &bull; {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>
    </div>
</body>
</html>