<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        @if(!$submitted)
            {{-- Form Input --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-8 text-white">
                    <h2 class="text-3xl font-bold">Form Pembayaran Kos</h2>
                    <p class="mt-2 text-blue-100">Silakan isi form di bawah ini dan upload bukti pembayaran Anda</p>
                </div>

                <div class="px-6 py-8">
                    @if (session()->has('error'))
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    <form wire:submit.prevent="submit">
                        <div class="space-y-6">
                            {{-- Data Penghuni --}}
                            <div class="border-b pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Penghuni</h3>
                                <div class="space-y-4">
                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Nama Lengkap <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="nama_penghuni" wire:input.debounce.300ms="searchPenghuni"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        @error('nama_penghuni') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                                        @if($showSuggest)
                                            <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg">
                                                @foreach($suggestions as $s)
                                                    <li wire:click="selectPenghuni({{ $s->id }})"
                                                        class="px-4 py-2 hover:bg-blue-50 cursor-pointer">
                                                        {{ $s->nama }} - {{ $s->email }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>

                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Email <span class="text-red-500">*</span>
                                        </label>
                                        <input type="email" wire:model="email" wire:input.debounce.300ms="searchPenghuni"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Nomor HP <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" wire:model="no_hp"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                               placeholder="08xxxxxxxxxx">
                                        @error('no_hp') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Data Pembayaran --}}
                            <div class="border-b pb-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Pembayaran</h3>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Periode Mulai <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" wire:model="periode_mulai"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                            @error('periode_mulai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                Periode Selesai <span class="text-red-500">*</span>
                                            </label>
                                            <input type="date" wire:model="periode_selesai"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                            @error('periode_selesai') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Jumlah Pembayaran (Rp) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number" wire:model="jumlah" step="0.01"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                               placeholder="0">
                                        @error('jumlah') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Metode Pembayaran <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="metode"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                            <option value="transfer">Transfer Bank</option>
                                            <option value="e-wallet">E-Wallet (OVO, Gopay, DANA)</option>
                                            <option value="cash">Cash (Tunai)</option>
                                        </select>
                                        @error('metode') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Upload Bukti Pembayaran <span class="text-red-500">*</span>
                                        </label>
                                        <input type="file" wire:model="bukti_file" accept=".jpg,.jpeg,.png,.pdf"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                        <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, PDF (Maks: 2MB)</p>
                                        @error('bukti_file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        @if($bukti_file)
                                            <div class="mt-2 text-sm text-green-600">File dipilih: {{ $bukti_file->getClientOriginalName() }}</div>
                                        @endif
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                                        <textarea wire:model="catatan" rows="3"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                                  placeholder="Opsional: Tambahkan keterangan tambahan"></textarea>
                                        @error('catatan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="flex justify-end">
                                <button type="submit"
                                        class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Kirim Pembayaran
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @else
            {{-- Success Message --}}
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-500 px-6 py-8 text-white text-center">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-3xl font-bold">Pembayaran Berhasil Dikirim!</h2>
                    <p class="mt-2 text-green-100">Terima kasih, pembayaran Anda sedang kami proses</p>
                </div>

                <div class="px-6 py-8 text-center">
                    <div class="mb-6">
                        <p class="text-gray-600 mb-2">Kode Transaksi Anda:</p>
                        <div class="bg-gray-100 rounded-lg p-4 inline-block">
                            <p class="text-2xl font-bold text-gray-900 font-mono">{{ $kode_transaksi }}</p>
                        </div>
                    </div>

                    <div class="mb-6 text-sm text-gray-600">
                        <p>Silakan simpan kode transaksi ini untuk memantau status pembayaran Anda.</p>
                        <p class="mt-2">Kami akan mengirimkan konfirmasi melalui email setelah pembayaran diverifikasi.</p>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <button wire:click="resetForm"
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Kirim Pembayaran Lain
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>