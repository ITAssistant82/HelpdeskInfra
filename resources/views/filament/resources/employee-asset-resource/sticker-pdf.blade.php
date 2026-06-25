<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Stiker - {{ $asset->asset_code }}</title>
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { width: 210mm; height: 297mm; margin: 0; padding: 0; background: white; }
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 6pt; position: relative; }
        .label { width: 92mm; height: 45mm; padding: 1.5mm; text-align: center; border: 0.5px solid #d1d5db; position: absolute; left: 10mm; top: 30.5mm; }
        .bar { background: #1e3a5f; color: white; padding: 1mm; margin: -1.5mm -1.5mm 1mm -1.5mm; }
        .bar .title { font-size: 6pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .bar .sub { font-size: 4.5pt; opacity: 0.8; }
        .bar .code { display: inline-block; background: rgba(255,255,255,0.15); padding: 0.3mm 1.5mm; font-family: 'Courier New', monospace; font-size: 5.5pt; font-weight: bold; margin-top: 0.5mm; }
        .qr { display: flex; flex-direction: column; align-items: center; justify-content: center; }
        .qr img { width: 30mm; height: 30mm; display: block; }
        .qr .hint { font-size: 3.5pt; color: #9ca3af; text-transform: uppercase; margin-top: 0.2mm; }
    </style>
</head>
<body>
    <div class="label">
        <div class="bar">
            <div class="title">IT Infrastructure</div>
            <div class="sub">Asset Management</div>
            <div class="code">{{ $asset->asset_code }}</div>
        </div>
        <div class="qr">
            <img src="data:image/png;base64,{{ $barcode }}" alt="QR">
            <div class="hint">Scan QR</div>
        </div>
    </div>
</body>
</html>
