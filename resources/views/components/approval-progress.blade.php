<div class="fi-ta-ctn overflow-x-auto">
    @if($approvals && $approvals->isNotEmpty())
        <table class="fi-ta-table w-full">
            <thead>
                <tr class="fi-ta-row border-b border-gray-200 dark:border-gray-700">
                    <th class="fi-ta-header-cell px-3 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400 w-12">#</th>
                    <th class="fi-ta-header-cell px-3 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Approver</th>
                    <th class="fi-ta-header-cell px-3 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                    <th class="fi-ta-header-cell px-3 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Catatan</th>
                    <th class="fi-ta-header-cell px-3 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($approvals as $a)
                    <tr class="fi-ta-row border-b border-gray-100 dark:border-gray-700">
                        <td class="fi-ta-cell px-3 py-2 text-sm">{{ $a->sequence_order }}</td>
                        <td class="fi-ta-cell px-3 py-2 text-sm">{{ $a->approver?->name ?? ucfirst($a->role_name ?? '-') }}</td>
                        <td class="fi-ta-cell px-3 py-2 text-sm">
                            @if($a->status === 'approved')
                                <span class="fi-badge fi-color-success bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400 px-2 py-0.5 rounded-md text-xs font-medium">Disetujui</span>
                            @elseif($a->status === 'rejected')
                                <span class="fi-badge fi-color-danger bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400 px-2 py-0.5 rounded-md text-xs font-medium">Ditolak</span>
                            @else
                                <span class="fi-badge fi-color-gray bg-gray-100 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400 px-2 py-0.5 rounded-md text-xs font-medium">Menunggu</span>
                            @endif
                        </td>
                        <td class="fi-ta-cell px-3 py-2 text-sm">{{ $a->note ?? '-' }}</td>
                        <td class="fi-ta-cell px-3 py-2 text-sm">{{ $a->acted_at ? $a->acted_at->format('d/m/Y H:i') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 px-3 py-2">Tidak ada data approval</p>
    @endif
</div>
