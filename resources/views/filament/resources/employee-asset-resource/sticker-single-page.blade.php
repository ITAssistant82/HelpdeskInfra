<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stiker Halaman {{ $page_number }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 5mm;
            background: white;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 6pt;
        }

        table.grid {
            width: 100%;
            border-collapse: collapse;
        }

        td.label {
            width: 92mm;
            height: 38mm;
            padding: 1.5mm;
            vertical-align: middle;
            text-align: center;
            border: 0.5px solid #d1d5db;
        }

        td.gap-h {
            width: 6mm;
            padding: 0;
            border: none;
        }

        tr.gap-v td {
            height: 3mm;
            padding: 0;
            border: none;
        }

        .bar {
            background: #1e3a5f;
            color: white;
            padding: 1mm;
            margin: -1.5mm -1.5mm 1mm -1.5mm;
        }
        .bar .title {
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .bar .sub {
            font-size: 4.5pt;
            opacity: 0.8;
        }
        .bar .code {
            display: inline-block;
            background: rgba(255,255,255,0.15);
            padding: 0.3mm 1.5mm;
            font-family: 'Courier New', monospace;
            font-size: 5.5pt;
            font-weight: bold;
            margin-top: 0.5mm;
        }

        .qr img {
            width: 16mm;
            height: 16mm;
        }
        .qr .hint {
            font-size: 3.5pt;
            color: #9ca3af;
            text-transform: uppercase;
            margin-top: 0.2mm;
        }

        .foot {
            border-top: 0.5px solid #d1d5db;
            padding-top: 0.5mm;
            margin-top: 0.5mm;
            font-size: 5pt;
            color: #374151;
            line-height: 1.2;
        }
        .foot .nik {
            font-size: 4.5pt;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <table class="grid">
        @for ($r = 0; $r < 5; $r++)
            @if ($r > 0)
            <tr class="gap-v"><td colspan="3"></td></tr>
            @endif
            <tr>
                @php $idx = $r * 2; @endphp
                <td class="label">
                    @if (isset($chunk[$idx]))
                        @php $a = $chunk[$idx]['asset']; @endphp
                        <div class="bar">
                            <div class="title">IT Infrastructure</div>
                            <div class="sub">Asset Management</div>
                            <div class="code">{{ $a->asset_code }}</div>
                        </div>
                        <div class="qr">
                            <img src="data:image/png;base64,{{ $chunk[$idx]['barcode'] }}" alt="QR">
                            <div class="hint">Scan QR</div>
                        </div>
                        <div class="foot">
                            {{ $a->brand ?? '-' }} {{ $a->model ?? '-' }}
                            @if ($a->serial_number) | S/N: {{ $a->serial_number }} @endif
                            <div class="nik">{{ $a->employee?->nik ?? '-' }} - {{ $a->employee?->full_name ?? '-' }}</div>
                        </div>
                    @endif
                </td>
                <td class="gap-h"></td>
                @php $idx = $r * 2 + 1; @endphp
                <td class="label">
                    @if (isset($chunk[$idx]))
                        @php $a = $chunk[$idx]['asset']; @endphp
                        <div class="bar">
                            <div class="title">IT Infrastructure</div>
                            <div class="sub">Asset Management</div>
                            <div class="code">{{ $a->asset_code }}</div>
                        </div>
                        <div class="qr">
                            <img src="data:image/png;base64,{{ $chunk[$idx]['barcode'] }}" alt="QR">
                            <div class="hint">Scan QR</div>
                        </div>
                        <div class="foot">
                            {{ $a->brand ?? '-' }} {{ $a->model ?? '-' }}
                            @if ($a->serial_number) | S/N: {{ $a->serial_number }} @endif
                            <div class="nik">{{ $a->employee?->nik ?? '-' }} - {{ $a->employee?->full_name ?? '-' }}</div>
                        </div>
                    @endif
                </td>
            </tr>
        @endfor
    </table>
</body>
</html>
