<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 space-y-8">

    {{-- Page Title --}}
    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard</h1>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Total Penghuni --}}
        <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 rounded-2xl p-6 border border-cyan-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-cyan-700">Total Penghuni</p>
                    <p class="text-4xl font-extrabold text-cyan-900 mt-2">{{ $totalPenghuni }}</p>
                </div>
                <div class="p-3 bg-cyan-200/60 rounded-xl">
                    <svg class="w-7 h-7 text-cyan-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Kamar --}}
        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-6 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-700">Total Kamar</p>
                    <p class="text-4xl font-extrabold text-slate-900 mt-2">{{ $totalKamar }}</p>
                </div>
                <div class="p-3 bg-slate-200/60 rounded-xl">
                    <svg class="w-7 h-7 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Kamar Terisi --}}
        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl p-6 border border-emerald-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-emerald-700">Kamar Terisi</p>
                    <p class="text-4xl font-extrabold text-emerald-900 mt-2">{{ $kamarTerisi }}</p>
                </div>
                <div class="p-3 bg-emerald-200/60 rounded-xl">
                    <svg class="w-7 h-7 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Okupansi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Okupansi</h2>
        <div class="flex items-center gap-8">
            <div class="flex-1">
                <div class="text-5xl font-extrabold text-gray-900">{{ $okupansi }}%</div>
                <div class="text-base text-gray-500 mt-1">Persentase kamar terisi</div>
            </div>
            {{-- Progress Ring --}}
            <div class="relative w-40 h-40">
                <svg class="w-full h-full" viewBox="0 0 36 36">
                    <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none"
                          d="M18 2.084 a 15.916 15.916 0 0 1 0 31.832 a 15.916 15.916 0 0 1 0 -31.832"/>
                    <path class="text-indigo-500" stroke="currentColor" stroke-width="3"
                          stroke-dasharray="{{ $okupansi }}, 100" stroke-linecap="round" fill="none"
                          d="M18 2.084 a 15.916 15.916 0 0 1 0 31.832 a 15.916 15.916 0 0 1 0 -31.832"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center text-xl font-bold text-gray-700">
                    {{ $okupansi }}%
                </div>
            </div>
        </div>
    </div>

    {{-- Keterlambatan Pembayaran --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Keterlambatan Pembayaran</h2>
        @if($telatBayar->isEmpty())
            <p class="text-sm text-gray-500">Tidak ada keterlambatan pembayaran.</p>
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-700 uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3 text-left">Penghuni</th>
                            <th class="px-4 py-3 text-left">Kamar</th>
                            <th class="px-4 py-3 text-left">Periode Selesai</th>
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($telatBayar as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->penghuni->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->room->nomor_kamar ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $item->periode_selesai->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Telat</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>