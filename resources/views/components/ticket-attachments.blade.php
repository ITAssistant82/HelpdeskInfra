<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
    @if($attachments && $attachments->isNotEmpty())
        @foreach($attachments as $attachment)
            <div class="relative group border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-800">
                @if(str_starts_with($attachment->mime_type ?? '', 'image/'))
                    <a href="{{ route('storage.file', ['path' => $attachment->file_path]) }}" target="_blank">
                        <img src="{{ route('storage.file', ['path' => $attachment->file_path]) }}"
                             alt="{{ $attachment->file_name }}"
                             class="w-full h-28 object-cover"
                             loading="lazy">
                    </a>
                @elseif(str_starts_with($attachment->mime_type ?? '', 'application/pdf'))
                    <a href="{{ route('storage.file', ['path' => $attachment->file_path]) }}" target="_blank" class="block">
                        <div class="relative">
                            <iframe src="{{ route('storage.file', ['path' => $attachment->file_path]) }}#view=FitH"
                                    class="w-full h-28 pointer-events-none" loading="lazy"></iframe>
                            <div class="absolute inset-0 bg-transparent"></div>
                        </div>
                    </a>
                @else
                    <div class="flex items-center justify-center h-28 bg-gray-100 dark:bg-gray-700">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="px-2 py-1.5 text-xs text-gray-600 dark:text-gray-300 truncate" title="{{ $attachment->file_name }}">
                    {{ $attachment->file_name }}
                </div>
                <div class="px-2 pb-1.5 flex justify-between items-center">
                    <span class="text-[10px] text-gray-400">{{ $attachment->file_size ? round($attachment->file_size / 1024, 1) . ' KB' : '-' }}</span>
                    <a href="{{ route('storage.file', ['path' => $attachment->file_path]) }}"
                       target="_blank"
                       class="text-[10px] text-primary-600 hover:text-primary-500 dark:text-primary-400">
                        Download
                    </a>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-span-full text-sm text-gray-500 dark:text-gray-400 py-3">Belum ada lampiran</div>
    @endif
</div>
