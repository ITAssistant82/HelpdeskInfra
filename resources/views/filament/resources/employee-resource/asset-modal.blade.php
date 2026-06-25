<div style="padding: 4px;">
    {{-- Employee Header --}}
    <div style="text-align: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #f3f4f6;">
        <div style="font-size: 16px; font-weight: 700; color: #111827;">{{ $employee->full_name }}</div>
        <div style="font-size: 12px; color: #6b7280; margin-top: 2px;">{{ $employee->nik }} &middot; {{ $employee->prodi_unit_kerja }}</div>
    </div>

    {{-- Stats Row --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px;">
        <tr>
            <td width="33%" style="padding: 0 4px;">
                <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; text-align: center; background: #fff;">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #9ca3af;">Total Asset</div>
                    <div style="margin-top: 6px; font-size: 26px; font-weight: 800; color: #f59e0b;">{{ $employee->assets->count() }}</div>
                </div>
            </td>
            <td width="33%" style="padding: 0 4px;">
                <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; text-align: center; background: #fff;">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #9ca3af;">Baik</div>
                    <div style="margin-top: 6px; font-size: 26px; font-weight: 800; color: #10b981;">{{ $employee->assets->where('condition', 'Baik')->count() }}</div>
                </div>
            </td>
            <td width="33%" style="padding: 0 4px;">
                <div style="border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; text-align: center; background: #fff;">
                    <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #9ca3af;">Perlu / Rusak</div>
                    <div style="margin-top: 6px; font-size: 26px; font-weight: 800; color: #ef4444;">{{ $employee->assets->where('condition', 'Perlu Perawatan')->count() + $employee->assets->where('condition', 'Rusak')->count() }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Assets --}}
    @forelse ($employee->assets as $asset)
        <div style="border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: 12px; background: #fff; overflow: hidden;">
            {{-- Asset Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; font-weight: 800; letter-spacing: 1px; color: #92400e; background: #fef3c7; padding: 3px 10px; border-radius: 5px;">{{ $asset->asset_code }}</span>
                    <span style="font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 5px;
                        {{ $asset->asset_type === 'Laptop' ? 'background: #fef9c3; color: #854d0e;' : '' }}
                        {{ $asset->asset_type === 'PC' ? 'background: #dbeafe; color: #1e40af;' : '' }}
                        {{ $asset->asset_type === 'Smartphone' ? 'background: #fce7f3; color: #9d174d;' : '' }}
                        {{ !in_array($asset->asset_type, ['Laptop','PC','Smartphone']) ? 'background: #d1fae5; color: #065f46;' : '' }}
                    ">{{ $asset->asset_type }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px;">
                    <span style="padding: 2px 10px; border-radius: 12px; font-weight: 700; font-size: 11px;
                        {{ $asset->condition === 'Baik' ? 'background: #d1fae5; color: #059669;' : '' }}
                        {{ $asset->condition === 'Perlu Perawatan' ? 'background: #fef3c7; color: #d97706;' : '' }}
                        {{ $asset->condition === 'Rusak' ? 'background: #fee2e2; color: #dc2626;' : '' }}
                    ">{{ $asset->condition }}</span>
                    @if ($asset->tahun_pembelian)
                        <span style="color: #6b7280; font-weight: 500;">
                            {{ $asset->usia }}
                            <span style="font-weight: 400; color: #9ca3af;">&nbsp;(
                                @php $umur = (int) $asset->usia @endphp
                                @if ($umur >= 10)
                                    <span style="color: #dc2626;">tua</span>
                                @elseif ($umur >= 5)
                                    <span style="color: #d97706;">cukup</span>
                                @else
                                    <span style="color: #059669;">baru</span>
                                @endif
                            )</span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Specs --}}
            <div style="padding: 0;">
                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 12px;">
                    <tr>
                        <td width="50%" style="padding: 8px 10px 8px 14px; border-bottom: 1px solid #f3f4f6; border-right: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Brand</span>
                            <span style="color: #111827; font-weight: 600; font-size: 13px;">{{ $asset->brand ?: '-' }}</span>
                        </td>
                        <td width="50%" style="padding: 8px 14px 8px 10px; border-bottom: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Model</span>
                            <span style="color: #111827; font-weight: 600; font-size: 13px;">{{ $asset->model ?: '-' }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px 8px 14px; border-bottom: 1px solid #f3f4f6; border-right: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Tahun Beli</span>
                            <span style="color: #111827; font-weight: 600;">{{ $asset->tahun_pembelian ?: '-' }}</span>
                        </td>
                        <td style="padding: 8px 14px 8px 10px; border-bottom: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">S/N</span>
                            <span style="color: #111827; font-weight: 600;">{{ $asset->serial_number ?: '-' }}</span>
                        </td>
                    </tr>
                    @if ($asset->os || $asset->processor)
                    <tr>
                        <td colspan="2" style="padding: 8px 14px; border-bottom: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Sistem</span>
                            <span style="color: #111827; font-weight: 600;">{{ $asset->os ?: '-' }} @if ($asset->processor) | {{ $asset->processor }} @endif</span>
                        </td>
                    </tr>
                    @endif
                    @if ($asset->memory_gb || $asset->hard_drive_gb)
                    <tr>
                        <td style="padding: 8px 10px 8px 14px; border-bottom: 1px solid #f3f4f6; border-right: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">RAM</span>
                            <span style="color: #111827; font-weight: 600;">{{ $asset->memory_gb ? $asset->memory_gb . ' GB' : '-' }}</span>
                        </td>
                        <td style="padding: 8px 14px 8px 10px; border-bottom: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Storage</span>
                            <span style="color: #111827; font-weight: 600;">{{ $asset->hard_drive_gb ? $asset->hard_drive_gb . ' GB' : '-' }}</span>
                        </td>
                    </tr>
                    @endif
                    @if ($asset->mainboard)
                    <tr>
                        <td colspan="2" style="padding: 8px 14px; border-bottom: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Mainboard</span>
                            <span style="color: #111827; font-weight: 600;">{{ $asset->mainboard }}</span>
                        </td>
                    </tr>
                    @endif
                    @if ($asset->monitor)
                    <tr>
                        <td colspan="2" style="padding: 8px 14px; border-bottom: 1px solid #f3f4f6;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Monitor</span>
                            <span style="color: #111827; font-weight: 600;">{{ $asset->monitor }}</span>
                        </td>
                    </tr>
                    @endif
                    @if ($asset->notes)
                    <tr>
                        <td colspan="2" style="padding: 8px 14px;">
                            <span style="color: #9ca3af; display: block; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Catatan</span>
                            <span style="color: #111827; font-weight: 600;">{{ $asset->notes }}</span>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    @empty
        <div style="border: 2px dashed #e5e7eb; border-radius: 10px; padding: 40px; text-align: center; background: #f9fafb;">
            <div style="font-size: 14px; font-weight: 700; color: #6b7280;">Belum ada perangkat</div>
            <div style="font-size: 12px; color: #9ca3af; margin-top: 4px;">Tambahkan melalui menu Asset Perangkat</div>
        </div>
    @endforelse
</div>
