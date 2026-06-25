<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Barcode - {{ $asset->asset_code }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: white;
            padding: 20px;
        }

        .barcode-container {
            max-width: 350px;
            margin: 0 auto;
            text-align: center;
        }

        .header {
            margin-bottom: 15px;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 12px;
        }

        .header h2 {
            color: #1f2937;
            font-size: 16px;
            margin-bottom: 3px;
        }

        .header .asset-code {
            color: #6b7280;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .info-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-table td.label {
            font-weight: 600;
            color: #374151;
            width: 35%;
        }

        .info-table td.value {
            color: #6b7280;
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-baik { background: #d1fae5; color: #065f46; }
        .badge-perlu { background: #fef3c7; color: #92400e; }
        .badge-rusak { background: #fee2e2; color: #991b1b; }

        .qrcode {
            margin: 15px 0;
        }

        .qrcode img {
            max-width: 200px;
            height: auto;
        }

        .qrcode .label {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 5px;
        }

        .barcode-text {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
            letter-spacing: 2px;
            margin-top: 8px;
        }

        .footer {
            margin-top: 15px;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        @media print {
            body { padding: 10px; }
            .barcode-container { max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="barcode-container">
        <div class="header">
            <h2>{{ $asset->brand }} {{ $asset->model }}</h2>
            <div class="asset-code">{{ $asset->asset_code }}</div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Tipe</td>
                <td class="value">{{ $asset->asset_type }}</td>
            </tr>
            <tr>
                <td class="label">Kondisi</td>
                <td class="value">
                    <span class="badge badge-{{ str_replace(' ', '', strtolower($asset->condition)) }}">{{ $asset->condition }}</span>
                </td>
            </tr>
            @if ($asset->serial_number)
            <tr>
                <td class="label">Serial</td>
                <td class="value">{{ $asset->serial_number }}</td>
            </tr>
            @endif
            @if ($asset->tahun_pembelian)
            <tr>
                <td class="label">Tahun Beli</td>
                <td class="value">{{ $asset->tahun_pembelian }}</td>
            </tr>
            @endif
            @if ($asset->employee)
            <tr>
                <td class="label">NIK</td>
                <td class="value">{{ $asset->employee->nik }}</td>
            </tr>
            <tr>
                <td class="label">Pemilik</td>
                <td class="value">{{ $asset->employee->full_name }}</td>
            </tr>
            @if ($asset->employee->prodi_unit_kerja)
            <tr>
                <td class="label">Unit</td>
                <td class="value">{{ $asset->employee->prodi_unit_kerja }}</td>
            </tr>
            @endif
            @endif
            @if ($asset->os)
            <tr>
                <td class="label">OS</td>
                <td class="value">{{ $asset->os }}</td>
            </tr>
            @endif
            @if ($asset->processor)
            <tr>
                <td class="label">Prosesor</td>
                <td class="value">{{ $asset->processor }}</td>
            </tr>
            @endif
            @if ($asset->memory_gb)
            <tr>
                <td class="label">RAM</td>
                <td class="value">{{ $asset->memory_gb }} GB</td>
            </tr>
            @endif
            @if ($asset->hard_drive_gb)
            <tr>
                <td class="label">Storage</td>
                <td class="value">{{ $asset->hard_drive_gb }} GB</td>
            </tr>
            @endif
            @if ($asset->assigned_at)
            <tr>
                <td class="label">Diberikan</td>
                <td class="value">{{ $asset->assigned_at->format('d/m/Y') }}</td>
            </tr>
            @endif
        </table>

        <div class="qrcode">
            <img src="data:image/png;base64,{{ $barcode }}" alt="QR Code">
            <div class="label">Scan QR untuk detail lengkap</div>
        </div>

        <div class="barcode-text">{{ $asset->asset_code }}</div>

        <div class="footer">
            <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
            <p>{{ config('app.name', 'Asset Management System') }}</p>
        </div>
    </div>
</body>
</html>