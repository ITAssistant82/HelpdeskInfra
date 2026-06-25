<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b">
                <th class="py-2 px-2 text-left font-medium whitespace-nowrap">No</th>
                <th class="py-2 px-2 text-left font-medium whitespace-nowrap">Host Name</th>
                <th class="py-2 px-2 text-left font-medium whitespace-nowrap">IP</th>
                <th class="py-2 px-2 text-left font-medium whitespace-nowrap">Product Name</th>
                <th class="py-2 px-2 text-left font-medium whitespace-nowrap">EOL</th>
                <th class="py-2 px-2 text-left font-medium whitespace-nowrap">EOS</th>
                <th class="py-2 px-2 text-left font-medium whitespace-nowrap">EOSL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $item)
            <tr class="border-b">
                <td class="py-1 px-2">{{ $i + 1 }}</td>
                <td class="py-1 px-2">{{ $item->host_name }}</td>
                <td class="py-1 px-2">{{ $item->ip ?? '-' }}</td>
                <td class="py-1 px-2">{{ $item->product_name ?? '-' }}</td>
                <td class="py-1 px-2">{{ $item->eol_announcement?->format('d M Y') ?? '-' }}</td>
                <td class="py-1 px-2">{{ $item->end_of_sale?->format('d M Y') ?? '-' }}</td>
                <td class="py-1 px-2">{{ $item->end_of_service_life?->format('d M Y') ?? '-' }}</td>
            </tr>
            @endforeach
            @if(count($items) === 0)
            <tr>
                <td colspan="7" class="py-4 text-center text-gray-500">Belum ada data</td>
            </tr>
            @endif
        </tbody>
    </table>
</div>
