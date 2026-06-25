@php
    $record = $this->record;
@endphp

<x-filament::page>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
            <div class="p-6 sm:p-10">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    @if($record->category)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide
                            {{ $record->category === 'Incident' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $record->category === 'Service Request' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $record->category === 'Hardware' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ $record->category === 'Software' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $record->category === 'Network' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $record->category === 'Account' ? 'bg-cyan-100 text-cyan-700' : '' }}
                            {{ $record->category === 'General' ? 'bg-gray-100 text-gray-700' : '' }}">
                            {{ $record->category }}
                        </span>
                    @endif
                    <span class="text-xs text-gray-500">{{ $record->created_at?->format('d F Y') }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white leading-tight mb-8">
                    {{ $record->title }}
                </h1>

                @if($record->content)
                    <div class="prose prose-sm sm:prose dark:prose-invert max-w-none">
                        {!! $record->content !!}
                    </div>
                @endif

                @if($record->attachments)
                    <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                        @foreach($record->attachments as $attachment)
                            @php
                                $filePath = is_string($attachment) ? $attachment : ($attachment['path'] ?? '');
                                $fileName = basename($filePath);
                                $fileUrl = Storage::disk('public')->url($filePath);
                                $isPdf = str_ends_with(strtolower($filePath), '.pdf');
                                $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $filePath);
                            @endphp

                            @if($isPdf)
                                <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <div class="flex items-center justify-end gap-2 px-4 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                        <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-red-600 text-white hover:bg-red-500">
                                            👁 Pratinjau
                                        </a>
                                        <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-500">
                                            ⬇ Unduh
                                        </a>
                                        {{-- <button onclick="window.open('{{ $fileUrl }}', '_blank')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-500" btnprimary>
                                            ⬇ Unduh
                                        </button> --}}
                                    </div>
                                    <div style="height: 500px; background: #f3f4f6;">
                                        <embed src="{{ $fileUrl }}#toolbar=0&navpanes=0" type="application/pdf" style="width:100%;height:100%;border:none;">
                                    </div>
                                </div>
                            @elseif($isImage)
                                <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <div class="flex justify-end px-4 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                        <a href="{{ $fileUrl }}" download class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-500">
                                            ⬇ Unduh
                                        </a>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 text-center">
                                        <img src="{{ $fileUrl }}" alt="{{ $fileName }}" class="max-w-full max-h-96 mx-auto rounded">
                                    </div>
                                </div>
                            @else
                                <a href="{{ $fileUrl }}" download class="inline-flex items-center justify-center gap-1.5 px-4 py-3 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-500 transition-all mb-2">
                                    ⬇ Unduh
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ \App\Filament\Resources\GuideResource::getUrl('index') }}" class="inline-flex px-5 py-2 rounded-lg text-sm font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                Kembali ke List
            </a>
        </div>
    </div>
</x-filament::page>
