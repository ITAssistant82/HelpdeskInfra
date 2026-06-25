<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Karyawan</p>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $totalEmployees }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Asset Dipakai</p>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $totalAssets }}</p>
                        </div>
                    </div>
                </div>

                @isset($totalStock)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Asset Stock (Belum Terpakai)</p>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $totalStock }}</p>
                        </div>
                    </div>
                </div>
                @endisset

                @isset($totalSwitches)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Network Switch</p>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $totalSwitches }}</p>
                        </div>
                    </div>
                </div>
                @endisset

                @isset($totalAccessPointItems)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Access Point</p>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $totalAccessPointItems }}</p>
                        </div>
                    </div>
                </div>
                @endisset

                @isset($totalAccessPoints)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lokasi Gedung AP</p>
                            <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ $totalAccessPoints }}</p>
                        </div>
                    </div>
                </div>
                @endisset

            </div>

            {{-- Kondisi Asset & Tipe Asset --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Kondisi Asset Dipakai</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-green-600 dark:text-green-400 font-medium">Baik</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $assetBaik }} ({{ $totalAssets > 0 ? round(($assetBaik / $totalAssets) * 100) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $totalAssets > 0 ? ($assetBaik / $totalAssets) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-yellow-600 dark:text-yellow-400 font-medium">Perlu Perawatan</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $assetPerawatan }} ({{ $totalAssets > 0 ? round(($assetPerawatan / $totalAssets) * 100) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-yellow-500 h-2.5 rounded-full" style="width: {{ $totalAssets > 0 ? ($assetPerawatan / $totalAssets) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-red-600 dark:text-red-400 font-medium">Rusak</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $assetRusak }} ({{ $totalAssets > 0 ? round(($assetRusak / $totalAssets) * 100) : 0 }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $totalAssets > 0 ? ($assetRusak / $totalAssets) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Asset per Jenis Perangkat</h3>
                    <div class="space-y-4">
                        @forelse($assetTypes as $type => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $type }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $totalAssets > 0 ? ($count / $totalAssets) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Belum ada data asset</p>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Asset Stock Section --}}
            @isset($totalStock)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Asset Stock (IT Belum Terpakai)</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Kondisi Stock</h4>
                        @forelse($stockCondition as $condition => $count)
                            <div class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $condition ?: 'Tidak diketahui' }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $count }} ({{ $totalStock > 0 ? round(($count / $totalStock) * 100) : 0 }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-purple-500 h-2.5 rounded-full" style="width: {{ $totalStock > 0 ? ($count / $totalStock) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Belum ada data stock</p>
                        @endforelse
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Jenis Stock</h4>
                        @forelse($stockTypes as $type => $count)
                            <div class="mb-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $type ?: 'Tidak diketahui' }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-indigo-500 h-2.5 rounded-full" style="width: {{ $totalStock > 0 ? ($count / $totalStock) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Belum ada data stock</p>
                        @endforelse
                    </div>
                </div>
            </div>

            @endisset

            {{-- Network Switch Section --}}
            @isset($totalSwitches)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Network Switch per Site</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">BSD</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $switchBSD }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-cyan-500 h-2.5 rounded-full" style="width: {{ $totalSwitches > 0 ? ($switchBSD / $totalSwitches) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300 font-medium">Cilandak</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $switchCilandak }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="bg-teal-500 h-2.5 rounded-full" style="width: {{ $totalSwitches > 0 ? ($switchCilandak / $totalSwitches) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Switch per Brand</h3>
                    <div class="space-y-4">
                        @forelse($switchBrands as $brand => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $brand ?: 'Tidak diketahui' }}</span>
                                    <span class="text-gray-600 dark:text-gray-400">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                    <div class="bg-orange-500 h-2.5 rounded-full" style="width: {{ $totalSwitches > 0 ? ($count / $totalSwitches) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Belum ada data switch</p>
                        @endforelse
                    </div>
                </div>

            </div>

            @endisset

            {{-- Access Point Section --}}
            @isset($apItemsBSD)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Access Point per Site</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200">BSD</h4>
                            <span class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">{{ $apItemsBSD }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total access point items</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-800 dark:text-gray-200">Cilandak</h4>
                            <span class="text-2xl font-bold text-teal-600 dark:text-teal-400">{{ $apItemsCilandak }}</span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total access point items</p>
                    </div>
                </div>
            </div>

            @endisset

            {{-- Latest Assets --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Asset Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">Kode Asset</th>
                                <th class="px-4 py-3">Pemilik</th>
                                <th class="px-4 py-3">Jenis</th>
                                <th class="px-4 py-3">Brand</th>
                                <th class="px-4 py-3">Kondisi</th>
                                <th class="px-4 py-3">Ditambahkan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestAssets as $asset)
                                <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $asset->asset_code }}</td>
                                    <td class="px-4 py-3">{{ $asset->employee?->full_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">{{ $asset->asset_type }}</td>
                                    <td class="px-4 py-3">{{ $asset->brand }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $badgeColor = match($asset->condition) {
                                                'Baik' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                                                'Perlu Perawatan' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                                'Rusak' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                            };
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $badgeColor }}">
                                            {{ $asset->condition }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $asset->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-3 text-center text-gray-500">Belum ada data asset</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
