<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Penghuni</h2>
        <p class="text-gray-600 mt-1">Kelola data penghuni kos Anda</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @foreach(['menunggu_verifikasi', 'aktif', 'ditolak', 'keluar'] as $status)
            @php
                $colors = [
                    'menunggu_verifikasi' => 'bg-yellow-100 border-yellow-300 text-yellow-800',
                    'aktif' => 'bg-green-100 border-green-300 text-green-800',
                    'ditolak' => 'bg-red-100 border-red-300 text-red-800',
                    'keluar' => 'bg-gray-100 border-gray-300 text-gray-800',
                ];
                $label = ucfirst(str_replace('_', ' ', $status));
            @endphp
            <div class="{{ $colors[$status] }} rounded-lg p-4 border">
                <div class="text-sm font-semibold">{{ $label }}</div>
                <div class="text-3xl font-bold mt-2">{{ $statusCounts[$status] }}</div>
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
                value="{{ url('/public/daftar-penghuni') }}" 
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


    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama, email, atau no HP..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="w-full md:w-64">
                <select wire:model.live="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                    <option value="aktif">Aktif</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>
            <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                + Tambah Penghuni
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Masuk</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($penghunis as $penghuni)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $penghuni->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $penghuni->email }}</div>
                                <div class="text-gray-500">{{ $penghuni->no_hp }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $penghuni->kamar?->nama ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badge = [
                                        'menunggu_verifikasi' => 'bg-yellow-100 text-yellow-800',
                                        'aktif' => 'bg-green-100 text-green-800',
                                        'ditolak' => 'bg-red-100 text-red-800',
                                        'keluar' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs font-semibold rounded-full {{ $badge[$penghuni->status] }}">
                                    {{ ucfirst(str_replace('_', ' ', $penghuni->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex gap-2">
                                    @if($penghuni->ktp)
                                        <a href="{{ Storage::url($penghuni->ktp) }}" target="_blank" class="text-blue-600 hover:underline">KTP</a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                    @if($penghuni->perjanjian)
                                        <a href="{{ Storage::url($penghuni->perjanjian) }}" target="_blank" class="text-blue-600 hover:underline">Perjanjian</a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $penghuni->tanggal_masuk?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($penghuni->status == 'menunggu_verifikasi')
                                        <button wire:click="openVerifyModal({{ $penghuni->id }})" class="text-blue-600 hover:underline font-semibold">Verifikasi</button>
                                    @endif
                                    <button wire:click="edit({{ $penghuni->id }})" class="text-indigo-600 hover:underline">Edit</button>
                                    <button wire:click="delete({{ $penghuni->id }})" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-600 hover:underline">Hapus</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data penghuni</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $penghunis->links() }}
        </div>
    </div>

    <!-- Modal Tambah/Edit -->
    @if($showModal)
        <div class="fixed inset-0 backdrop-blur-sm bg-black/30 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900">{{ $isEdit ? 'Edit Penghuni' : 'Tambah Penghuni Baru' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <form wire:submit.prevent="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nama *</label>
                            <input wire:model="nama" type="text" class="w-full px-3 py-2 border rounded-lg">
                            @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input wire:model="email" type="email" class="w-full px-3 py-2 border rounded-lg">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No HP</label>
                            <input wire:model="no_hp" type="text" class="w-full px-3 py-2 border rounded-lg">
                            @error('no_hp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea wire:model="alamat" rows="2" class="w-full px-3 py-2 border rounded-lg"></textarea>
                            @error('alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kamar</label>
                            <select wire:model="room_id" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">Pilih Kamar</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->nama }}</option>
                                @endforeach
                            </select>
                            @error('room_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status *</label>
                            <select wire:model="status" class="w-full px-3 py-2 border rounded-lg">
                                <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                                <option value="aktif">Aktif</option>
                                <option value="ditolak">Ditolak</option>
                                <option value="keluar">Keluar</option>
                            </select>
                            @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                            <input wire:model="tanggal_masuk" type="date" class="w-full px-3 py-2 border rounded-lg">
                            @error('tanggal_masuk') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Keluar</label>
                            <input wire:model="tanggal_keluar" type="date" class="w-full px-3 py-2 border rounded-lg">
                            @error('tanggal_keluar') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload KTP</label>
                            <input wire:model="ktp_file" type="file" accept="image/*,application/pdf" class="w-full px-3 py-2 border rounded-lg">
                            @error('ktp_file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="ktp_file" class="text-blue-500 text-xs mt-1">Uploading...</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Upload Perjanjian</label>
                            <input wire:model="perjanjian_file" type="file" accept="image/*,application/pdf" class="w-full px-3 py-2 border rounded-lg">
                            @error('perjanjian_file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            <div wire:loading wire:target="perjanjian_file" class="text-blue-500 text-xs mt-1">Uploading...</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Catatan</label>
                            <textarea wire:model="catatan" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                            @error('catatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Verifikasi -->
    @if($showVerifyModal)
        <div class="fixed inset-0 backdrop-blur-sm bg-black/30 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Verifikasi Penghuni</h3>

                <form wire:submit.prevent="verify">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Status Verifikasi *</label>
                        <select wire:model="verifyStatus" class="w-full px-3 py-2 border rounded-lg">
                            <option value="">-- Pilih --</option>
                            <option value="aktif">Terima</option>
                            <option value="ditolak">Tolak</option>
                        </select>
                        @error('verifyStatus') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea wire:model="verifyCatatan" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
                        @error('verifyCatatan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="closeVerifyModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>