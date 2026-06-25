<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Barcode Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 0;
            background: white;
        }

        .page {
            page-break-after: always;
            width: 210mm;
            height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .barcode-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15mm;
            height: 100%;
        }

        .barcode-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-inside: avoid;
            background: white;
        }

        .item-header {
            margin-bottom: 10px;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 8px;
        }

        .asset-type {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .asset-name {
            font-size: 12px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 3px;
        }

        .asset-code {
            font-size: 10px;
            color: #6b7280;
            font-family: monospace;
        }

        .item-info {
            font-size: 9px;
            margin-bottom: 10px;
            background: #f9fafb;
            padding: 8px;
            border-radius: 3px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 3px;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
        }

        .info-value {
            color: #1f2937;
            text-align: right;
        }

        .barcode-img {
            text-align: center;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
        }

        .barcode-img img {
            max-width: 140px;
            height: auto;
        }

        .barcode-code {
            text-align: center;
            font-family: monospace;
            font-weight: 600;
            font-size: 11px;
            color: #1f2937;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        @media print {
            .page {
                margin: 0;
                padding: 10mm;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $barcodes_per_page = 6;
        $chunks = collect($barcodes)->chunk($barcodes_per_page);
    @endphp

    @foreach ($chunks as $chunk)
        <div class="page">
            <div class="barcode-grid">
                @foreach ($chunk as $item)
                    <div class="barcode-item">
                        <div class="item-header">
                            <div class="asset-type">{{ $item['asset']->asset_type }}</div>
                            <div class="asset-name">{{ $item['asset']->brand }} {{ $item['asset']->model }}</div>
                            <div class="asset-code">{{ $item['asset']->asset_code }}</div>
                        </div>

                        <div class="item-info">
                            <div class="info-row">
                                <span class="info-label">Employee:</span>
                                <span class="info-value">{{ $item['asset']->employee->nik }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Kondisi:</span>
                                <span class="info-value">{{ $item['asset']->condition }}</span>
                            </div>
                        </div>

                        <div class="barcode-img">
                            <img src="data:image/png;base64,{{ $item['barcode'] }}" alt="Barcode">
                        </div>

                        <div class="barcode-code">{{ $item['asset']->asset_code }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</body>
</html>
