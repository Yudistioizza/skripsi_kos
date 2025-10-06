<div class="mx-auto p-6 space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800 tracking-tight">
            Laporan Keuangan
        </h1>
        <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500">
            <span>{{ now()->format('d M Y') }}</span>
            <span>•</span>
            <span>{{ $transaksi->count() }} transaksi</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Tanggal Awal -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Awal</label>
                <input type="date" wire:model="startDate"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Tanggal Akhir -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Akhir</label>
                <input type="date" wire:model="endDate"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select wire:model="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Status</option>
                    <option value="lunas">Lunas</option>
                    <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                    <option value="belum_lunas">Belum Lunas</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="jatuh_tempo">Jatuh Tempo</option>
                </select>
            </div>

            <!-- Reset -->
            <div class="flex items-end">
                <button wire:click="$set('status', '')"
                        class="w-full sm:w-auto px-4 py-2 border border-gray-300 text-gray-700 rounded-lg
                               hover:bg-gray-50 active:bg-gray-100 transition">
                    Reset
                </button>
            </div>
        </div>
    </div>
    
    <!-- Rekap -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow text-white p-5">
            <div class="text-sm opacity-90">Total Pemasukan</div>
            <div class="text-3xl font-bold tracking-tight">
                Rp {{ number_format($this->total, 0, ',', '.') }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500">Transaksi</div>
                <div class="text-2xl font-semibold text-gray-800">{{ $transaksi->count() }}</div>
            </div>
            <div class="text-indigo-500">
                <svg class="w-10 h-10 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Tabel Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Tabel Header dengan Export Buttons -->
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">Daftar Transaksi</h2>
                <p class="text-sm text-gray-500 mt-1">Menampilkan {{ $transaksi->count() }} data</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="exportExcel"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg
                               hover:bg-green-700 active:bg-green-800 transition text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export Excel
                </button>
                <button wire:click="exportPdf"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg
                               hover:bg-red-700 active:bg-red-800 transition text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Tabel Content -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 font-medium">Tanggal</th>
                        <th class="px-5 py-3 font-medium">Kode Transaksi</th>
                        <th class="px-5 py-3 font-medium">Penghuni</th>
                        <th class="px-5 py-3 font-medium">Kamar</th>
                        <th class="px-5 py-3 font-medium text-right">Jumlah</th>
                        <th class="px-5 py-3 font-medium text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($this->transaksi as $trx)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                {{ $trx->created_at->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-gray-900">
                                {{ $trx->kode_transaksi }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                {{ $trx->penghuni->nama ?? '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                {{ $trx->room->nama ?? '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right font-semibold">
                                Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                @php
                                    $badge = match($trx->status) {
                                        'lunas' => 'bg-green-100 text-green-700',
                                        'menunggu_verifikasi' => 'bg-yellow-100 text-yellow-700',
                                        'belum_lunas' => 'bg-red-100 text-red-700',
                                        'ditolak' => 'bg-gray-100 text-gray-700',
                                        'jatuh_tempo' => 'bg-orange-100 text-orange-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                    {{ ucfirst(str_replace('_', ' ', $trx->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                Tidak ada data untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>