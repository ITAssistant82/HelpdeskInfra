<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Detail - {{ $asset->asset_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #f59e0b;
            --primary-dark: #d97706;
            --primary-light: #fef3c7;
            --dark: #1f2937;
            --dark-secondary: #6b7280;
            --light: #f9fafb;
            --border: #e5e7eb;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .container {
            max-width: 600px;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 30px 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            font-size: 13px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 600;
            opacity: 0.95;
        }

        .asset-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .content {
            padding: 30px 25px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .content::-webkit-scrollbar {
            width: 6px;
        }

        .content::-webkit-scrollbar-track {
            background: var(--light);
            border-radius: 10px;
        }

        .content::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
        }

        .content::-webkit-scrollbar-thumb:hover {
            background: var(--dark-secondary);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-danger {
            background: #fee2e2;
            border-left-color: var(--danger);
            color: #991b1b;
        }

        .alert-icon {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--dark-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-icon {
            width: 4px;
            height: 16px;
            background: var(--primary);
            border-radius: 2px;
        }

        .info-group {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 14px;
            background: var(--light);
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }

        .info-item:hover {
            border-color: var(--primary);
            background: linear-gradient(135deg, var(--light) 0%, var(--primary-light) 100%);
        }

        .info-label {
            font-size: 12px;
            color: var(--dark-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-value {
            font-size: 14px;
            color: var(--dark);
            font-weight: 600;
            text-align: right;
            max-width: 65%;
            word-break: break-word;
        }

        .spec-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .spec-item {
            padding: 12px 14px;
            background: var(--light);
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.2s ease;
        }

        .spec-item:hover {
            border-color: var(--primary);
            background: linear-gradient(135deg, var(--light) 0%, var(--primary-light) 100%);
        }

        .spec-label {
            font-size: 10px;
            color: var(--dark-secondary);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 6px;
            display: block;
        }

        .spec-value {
            font-size: 13px;
            color: var(--dark);
            font-weight: 600;
        }

        .condition-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .condition-baik {
            background: #d1fae5;
            color: #065f46;
        }

        .condition-perawatan {
            background: #fef3c7;
            color: #92400e;
        }

        .condition-rusak {
            background: #fee2e2;
            color: #991b1b;
        }

        .highlight {
            background: var(--primary-light);
            padding: 14px;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            margin-bottom: 20px;
        }

        .highlight-text {
            font-size: 13px;
            color: #92400e;
            font-weight: 500;
            line-height: 1.5;
        }

        .footer {
            background: linear-gradient(135deg, var(--light) 0%, white 100%);
            padding: 20px 25px;
            text-align: center;
            border-top: 1px solid var(--border);
        }

        .footer-text {
            font-size: 12px;
            color: var(--dark-secondary);
            margin-bottom: 12px;
            font-weight: 500;
        }

        .footer-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            color: #3b82f6;
            font-weight: 600;
            padding: 8px 12px;
            background: #eff6ff;
            border-radius: 6px;
            border: 1px solid #bfdbfe;
        }

        .footer-action::before {
            content: '✓';
            font-weight: 700;
        }

        .timestamp {
            font-size: 11px;
            color: var(--dark-secondary);
            margin-top: 12px;
            text-align: center;
        }

        @media (max-width: 600px) {
            .header {
                padding: 25px 20px;
            }

            .header-title {
                font-size: 24px;
            }

            .content {
                padding: 20px 15px;
            }

            .spec-grid {
                grid-template-columns: 1fr;
            }

            .info-value {
                max-width: 50%;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="header-content">
                    <div class="header-title">{{ $asset->asset_code }}</div>
                    <div class="header-subtitle">Asset IT Infrastructure</div>
                    <div class="asset-badge">{{ $asset->asset_type }}</div>
                </div>
            </div>

            <div class="content">
                @if ($error)
                <div class="alert alert-danger">
                    <div class="alert-icon">⚠️</div>
                    <div>{{ $error }}</div>
                </div>
                @else
                <div style="text-align: center; margin-bottom: 10px;">
                    <span class="footer-action" style="display: inline-flex; background: #d1fae5; border-color: var(--success); color: var(--success);">Data Terverifikasi</span>
                </div>
                @endif

                <!-- Device Info -->
                <div class="section">
                    <div class="section-title">
                        <div class="section-icon"></div>
                        Informasi Perangkat
                    </div>
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label">Brand</span>
                            <span class="info-value">{{ $asset->brand ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Model</span>
                            <span class="info-value">{{ $asset->model ?? '-' }}</span>
                        </div>
                        @if ($asset->serial_number)
                        <div class="info-item">
                            <span class="info-label">Serial Number</span>
                            <span class="info-value" style="font-family: 'Courier New', monospace; letter-spacing: 0.5px;">{{ $asset->serial_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="section">
                    <div class="section-title">
                        <div class="section-icon"></div>
                        Pemilik Asset
                    </div>
                    <div class="info-group">
                        @if ($asset->employee)
                        <div class="info-item">
                            <span class="info-label">NIK</span>
                            <span class="info-value" style="font-family: 'Courier New', monospace;">{{ $asset->employee->nik }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-value">{{ $asset->employee->full_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Unit Kerja</span>
                            <span class="info-value">{{ $asset->employee->prodi_unit_kerja }}</span>
                        </div>
                        @if ($asset->employee->email)
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value" style="font-size: 12px;">{{ $asset->employee->email }}</span>
                        </div>
                        @endif
                        @else
                        <div style="padding: 15px; background: #fee2e2; border-radius: 8px; border-left: 4px solid var(--danger); color: #991b1b; font-weight: 500;">
                            ⚠️ Tidak ada data pemilik untuk asset ini
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Hardware Specs -->
                @if ($asset->os || $asset->processor || $asset->memory_gb || $asset->hard_drive_gb || $asset->monitor || $asset->mainboard)
                <div class="section">
                    <div class="section-title">
                        <div class="section-icon"></div>
                        Spesifikasi Hardware
                    </div>
                    <div class="spec-grid">
                        @if ($asset->os)
                        <div class="spec-item">
                            <span class="spec-label">OS</span>
                            <span class="spec-value">{{ $asset->os }}</span>
                        </div>
                        @endif
                        @if ($asset->processor)
                        <div class="spec-item">
                            <span class="spec-label">Processor</span>
                            <span class="spec-value">{{ $asset->processor }}</span>
                        </div>
                        @endif
                        @if ($asset->memory_gb)
                        <div class="spec-item">
                            <span class="spec-label">RAM</span>
                            <span class="spec-value">{{ $asset->memory_gb }} GB</span>
                        </div>
                        @endif
                        @if ($asset->hard_drive_gb)
                        <div class="spec-item">
                            <span class="spec-label">Storage</span>
                            <span class="spec-value">{{ $asset->hard_drive_gb }} GB</span>
                        </div>
                        @endif
                        @if ($asset->monitor)
                        <div class="spec-item">
                            <span class="spec-label">Monitor</span>
                            <span class="spec-value">{{ $asset->monitor }}</span>
                        </div>
                        @endif
                        @if ($asset->mainboard)
                        <div class="spec-item">
                            <span class="spec-label">Mainboard</span>
                            <span class="spec-value">{{ $asset->mainboard }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Status Info -->
                <div class="section">
                    <div class="section-title">
                        <div class="section-icon"></div>
                        Status & Tanggal
                    </div>
                    <div class="info-group">
                        <div class="info-item">
                            <span class="info-label">Kondisi</span>
                            <span class="info-value">
                                <span class="condition-badge condition-{{ strtolower(str_replace(' ', '', $asset->condition)) }}">
                                    {{ $asset->condition }}
                                </span>
                            </span>
                        </div>
                        @if ($asset->assigned_at)
                        <div class="info-item">
                            <span class="info-label">Tanggal Diberikan</span>
                            <span class="info-value">{{ $asset->assigned_at->format('d M Y') }}</span>
                        </div>
                        @endif
                        @if ($asset->tahun_pembelian)
                        <div class="info-item">
                            <span class="info-label">Tahun Pembelian</span>
                            <span class="info-value">{{ $asset->tahun_pembelian }}</span>
                        </div>
                        @endif
                        @if ($asset->created_at)
                        <div class="info-item">
                            <span class="info-label">Dicatat pada</span>
                            <span class="info-value">{{ $asset->created_at->format('d M Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if ($asset->notes)
                <div class="highlight">
                    <div class="highlight-text">
                        <strong>📝 Catatan Tambahan:</strong><br>
                        {{ $asset->notes }}
                    </div>
                </div>
                @endif
            </div>

            <div class="footer">
                <div class="footer-text">
                    Informasi Asset IT Infrastructure
                </div>
                <div class="footer-action">
                    Data terkonfirmasi valid
                </div>
                <div class="timestamp">
                    Dibaca pada: {{ now()->format('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
