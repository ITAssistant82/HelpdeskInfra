@php
    $record = $getRecord();
    $url = \App\Filament\Resources\GuideResource::getUrl('view', ['record' => $record]);
@endphp

<a href="{{ $url }}" class="block h-full group">
    <div class="flex flex-col h-full rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-out">
        @if($record->category)
            <span class="inline-flex self-start px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wide mb-4
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

        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug mb-4 line-clamp-2 flex-1">
            {{ $record->title }}
        </h3>

        <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <span class="text-xs text-gray-400">
                {{ $record->created_at?->format('d M Y') }}
            </span>
            <span class="text-xs font-semibold text-primary-600 dark:text-primary-400">
                Baca
            </span>
        </div>
    </div>
</a>
