{{-- resources/views/livewire/pembayaran/pembayaran-manager.blade.php --}}
<div class="p-6">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Pembayaran</h2>
        <p class="text-gray-600">Kelola pembayaran dan verifikasi transaksi</p>
    </div>

    {{-- Notifikasi --}}
    @if (session()->has('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Alert / Ringkasan status pembayaran --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @foreach(['menunggu_verifikasi', 'lunas', 'ditolak', 'jatuh_tempo'] as $status)
            @php
                $colors = [
                    'menunggu_verifikasi' => 'bg-yellow-100 border-yellow-300 text-yellow-800',
                    'lunas'               => 'bg-green-100 border-green-300 text-green-800',
                    'ditolak'             => 'bg-red-100 border-red-300 text-red-800',
                    'jatuh_tempo'         => 'bg-gray-100 border-gray-300 text-gray-800',
                ];
                $label = ucfirst(str_replace('_', ' ', $status));
                $count = match ($status) {
                    'menunggu_verifikasi' => $menungguVerifikasi,
                    'jatuh_tempo'         => $jatuhTempo,
                    'lunas'               => \App\Models\Pembayaran::where('status', 'lunas')->count(),
                    'ditolak'             => \App\Models\Pembayaran::where('status', 'ditolak')->count(),
                };
            @endphp
            <div class="{{ $colors[$status] }} rounded-lg p-4 border">
                <div class="text-sm font-semibold">{{ $label }}</div>
                <div class="text-3xl font-bold mt-2">{{ $count }}</div>
            </div>
        @endforeach
    </div>

    <div class="mb-4 p-4 bg-white rounded-lg shadow-sm">
        <h2 class="text-sm font-semibold text-gray-700 mb-1">
            Link Form Registrasi Penghuni
        </h2>
        <p class="text-xs text-gray-500 mb-3">
            Bagikan link ini ke calon penghuni kos Anda
        </p>
        
        <div class="flex items-center space-x-2">
            <input 
                id="form-link" 
                type="text" 
                value="{{ url('/public/upload-bukti-pembayaran') }}" 
                readonly 
                class="flex-1 px-2 py-1 text-xs border rounded bg-gray-50 text-gray-600 focus:outline-none"
            >
            <button 
                onclick="navigator.clipboard.writeText(document.getElementById('form-link').value)" 
                class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 focus:ring focus:ring-blue-300 transition"
            >
                Copy
            </button>
        </div>
    </div>

    {{-- Filter & Tambah --}}
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 flex flex-col md:flex-row gap-4">
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Cari kode transaksi atau nama penghuni..."
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <select wire:model.live="filterStatus"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="all">Semua Status</option>
                    <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                    <option value="lunas">Lunas</option>
                    <option value="belum_lunas">Belum Lunas</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="jatuh_tempo">Jatuh Tempo</option>
                </select>
            </div>
            <button wire:click="create"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                + Tambah Pembayaran
            </button>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penghuni</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bukti</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pembayaran as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->kode_transaksi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->penghuni->nama ?? '-' }}
                            @if($item->room)
                                <br><span class="text-xs text-gray-500">{{ $item->room->nama }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ optional($item->periode_mulai)->format('d/m/Y') }} -<br>
                            {{ optional($item->periode_selesai)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm capitalize">{{ $item->metode ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badge = [
                                    'menunggu_verifikasi' => 'bg-yellow-100 text-yellow-800',
                                    'lunas' => 'bg-green-100 text-green-800',
                                    'belum_lunas' => 'bg-gray-100 text-gray-800',
                                    'ditolak' => 'bg-red-100 text-red-800',
                                    'jatuh_tempo' => 'bg-red-100 text-red-800',
                                ][$item->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $badge }}">
                                {{ str_replace('_', ' ', ucfirst($item->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->bukti->count())
                                <button wire:click="viewBukti({{ $item->id }})" class="text-blue-600 hover:text-blue-800">
                                    {{ $item->bukti->count() }} file
                                </button>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                @if($item->status === 'menunggu_verifikasi')
                                    <button wire:click="openVerifikasi({{ $item->id }})"
                                            class="text-green-600 hover:text-green-900" title="Verifikasi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @endif
                                <button wire:click="edit({{ $item->id }})"
                                        class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button wire:click="openDeleteModal('pembayaran', {{ $item->id }}, 'Pembayaran {{ $item->kode_transaksi }}')"class="text-red-600 hover:text-red-900" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data pembayaran</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">{{ $pembayaran->links() }}</div>
    </div>

    {{-- Modal Tambah/Edit --}}
    @if($showModal)
        <div class="fixed inset-0 backdrop-blur-sm bg-black/30 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">{{ $pembayaranId ? 'Edit' : 'Tambah' }} Pembayaran</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penghuni *</label>
                            <select wire:model.live="penghuni_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Penghuni</option>
                                @foreach($penghuni as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                            @error('penghuni_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Periode Mulai *</label>
                                <input type="date" wire:model.live="periode_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @error('periode_mulai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Periode Selesai *</label>
                                <input type="date" wire:model.live="periode_selesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @error('periode_selesai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp) *</label>
                            <input type="number" step="0.01" wire:model.live="jumlah" placeholder="0" {{ $penghuni_id ? 'readonly' : '' }} class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('jumlah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode *</label>
                            <select wire:model="metode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="cash">Cash</option>
                                <option value="transfer">Transfer</option>
                                <option value="e-wallet">E-Wallet</option>
                            </select>
                            @error('metode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bukti</label>
                            <input type="file" wire:model="bukti_file" accept=".jpg,.jpeg,.png,.pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF (Max 2MB)</p>
                            @error('bukti_file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            @if($bukti_file)
                                <div class="mt-2 text-sm text-green-600">File dipilih: {{ $bukti_file->getClientOriginalName() }}</div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea wire:model="catatan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('catatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ $pembayaranId ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Verifikasi --}}
    @if($showVerifikasiModal)
        <div class="fixed inset-0 backdrop-blur-sm bg-black/30 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Verifikasi Pembayaran</h3>
                    <button wire:click="closeVerifikasiModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <form wire:submit.prevent="verifikasi">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select wire:model="verifikasiStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="lunas">Lunas</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                            @error('verifikasiStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                            <textarea wire:model="verifikasiCatatan" rows="3" placeholder="Tambahkan catatan verifikasi..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('verifikasiCatatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="closeVerifikasiModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal View Bukti --}}
    @if($showBuktiModal && $viewBuktiPembayaran)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Bukti Pembayaran - {{ $viewBuktiPembayaran->kode_transaksi }}</h3>
                    <button wire:click="closeBuktiModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    @forelse($viewBuktiPembayaran->bukti as $bukti)
                        <div class="border rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                @if(in_array($bukti->tipe, ['jpg','jpeg','png']))
                                    <img src="{{ Storage::url($bukti->file_path) }}" alt="Bukti" class="w-20 h-20 object-cover rounded">
                                @else
                                    <div class="w-20 h-20 bg-gray-100 rounded flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ strtoupper($bukti->tipe) }}</p>
                                    <p class="text-xs text-gray-500">Diunggah: {{ $bukti->created_at->format('d/m/Y H:i') }}</p>
                                    @if($bukti->uploadedBy)
                                        <p class="text-xs text-gray-500">Oleh: {{ $bukti->uploadedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ Storage::url($bukti->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <button wire:click="openDeleteModal('bukti', {{ $bukti->id }}, 'Bukti {{ strtoupper($bukti->tipe) }} - {{ $viewBuktiPembayaran->kode_transaksi }}')"class="text-red-600 hover:text-red-800 p-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">Tidak ada bukti pembayaran</p>
                    @endforelse
                </div>
                <div class="flex justify-end mt-6">
                    <button wire:click="closeBuktiModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Tutup</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Delete Confirmation --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 backdrop-blur-sm bg-black/30 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Hapus</h3>
                </div>
            </div>
            
            <div class="mb-6">
                <p class="text-gray-600">Apakah Anda yakin ingin menghapus <strong>{{ $deleteItemName }}</strong>?</p>
                <p class="text-sm text-gray-500 mt-2">Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" wire:click="closeDeleteModal" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Batal
                </button>
                <button type="button" wire:click="confirmDelete" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50">
                    <span wire:loading wire:target="confirmDelete">Menghapus...</span>
                    <span wire:loading.remove wire:target="confirmDelete">Hapus</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>