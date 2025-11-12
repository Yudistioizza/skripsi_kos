<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    {{-- Page Title --}}
    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard</h1>

    {{-- Ringkasan Semua (Sejajar) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total Penghuni --}}
        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-2xl p-5 border border-cyan-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-cyan-700">Total Penghuni</p>
                    <p class="text-3xl font-extrabold text-cyan-900 mt-1">{{ $totalPenghuni }}</p>
                </div>
                <div class="p-2 bg-cyan-200/60 rounded-lg">
                    <svg class="w-6 h-6 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Kamar --}}
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-700">Total Kamar</p>
                    <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ $totalKamar }}</p>
                </div>
                <div class="p-2 bg-slate-200/60 rounded-lg">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Kamar Terisi --}}
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-5 border border-emerald-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-emerald-700">Kamar Terisi</p>
                    <p class="text-3xl font-extrabold text-emerald-900 mt-1">{{ $kamarTerisi }}</p>
                </div>
                <div class="p-2 bg-emerald-200/60 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Okupansi --}}
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-5 border border-purple-200 shadow-sm">
            <div class="text-center">
                <p class="text-xs font-semibold text-purple-700 mb-1">Okupansi</p>
                <div class="text-3xl font-extrabold text-purple-900">{{ $okupansi }}%</div>
                <div class="w-full bg-purple-200 rounded-full h-2 mt-2">
                    <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $okupansi }}%"></div>
                </div>
            </div>
        </div>

        {{-- Keterlambatan --}}
        <div class="bg-gradient-to-br from-rose-50 to-rose-100 rounded-2xl p-5 border border-rose-200 shadow-sm">
            <div class="text-center">
                <p class="text-xs font-semibold text-rose-700 mb-1">Keterlambatan</p>
                <div class="text-3xl font-extrabold text-rose-900">{{ $telatBayar->count() }}</div>
                <p class="text-xs text-rose-700 mt-1">penghuni</p>
            </div>
        </div>
    </div>

    {{-- Laporan Keuangan Tahunan --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Laporan Keuangan Tahunan</h2>
            <div class="text-sm text-gray-500">{{ now()->year }}</div>
        </div>

        {{-- Total Tahunan Card --}}
        <div class="bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl p-6 text-white mb-6">
            <p class="text-sm font-medium opacity-90">Total Pemasukan Tahun Ini</p>
            <p class="text-4xl font-extrabold mt-2">Rp {{ number_format($totalTahunan, 0, ',', '.') }}</p>
        </div>

        {{-- Grid untuk Tabel dan Grafik --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Tabel Bulanan (2 kolom) --}}
            <div class="lg:col-span-2">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Rekap Bulanan</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-300">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Bulan</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Pemasukan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach($dataBulan as $index => $bulan)
                                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $bulan['nama'] }}</td>
                                    <td class="px-4 py-3 text-right font-mono text-gray-900">
                                        {{ number_format($bulan['total'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-bold">
                                <td class="px-4 py-3 text-gray-900 border-t-2 border-gray-300">TOTAL</td>
                                <td class="px-4 py-3 text-right font-mono text-gray-900 border-t-2 border-gray-300">
                                    {{ number_format($totalTahunan, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Grafik Batang (1 kolom) --}}
            <div class="lg:col-span-1">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Visualisasi</h3>
                <div class="h-64 bg-gray-50 rounded-lg p-4 flex items-end justify-between border border-gray-200">
                    @foreach($dataBulan as $bulan)
                        @php
                            $maxValue = max(array_column($dataBulan, 'total'));
                            $maxHeight = 180;
                            $height = $maxValue > 0 ? ($bulan['total'] / $maxValue) * $maxHeight : 0;
                        @endphp
                        <div class="flex flex-col items-center flex-1 mx-1">
                            <div class="w-full bg-cyan-500 hover:bg-cyan-600 transition-colors rounded-t" 
                                 style="height: {{ $height }}px; min-height: 4px;"></div>
                            <span class="text-xs text-gray-500 mt-2 font-medium rotate-45 origin-left">{{ $bulan['nama'] }}</span>
                        </div>
                    @endforeach
                </div>
                
                {{-- Statistik Ringkas --}}
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    @php
                        $rataBulanan = $totalTahunan / 12;
                        $bulanTertinggi = collect($dataBulan)->sortByDesc('total')->first();
                    @endphp
                    <div class="text-xs text-gray-600 space-y-1">
                        <div>Rata-rata/bulan: <span class="font-mono font-semibold">Rp {{ number_format($rataBulanan, 0, ',', '.') }}</span></div>
                        @if($bulanTertinggi && $bulanTertinggi['total'] > 0)
                            <div>Tertinggi: {{ $bulanTertinggi['nama'] }} <span class="font-mono font-semibold">Rp {{ number_format($bulanTertinggi['total'], 0, ',', '.') }}</span></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>