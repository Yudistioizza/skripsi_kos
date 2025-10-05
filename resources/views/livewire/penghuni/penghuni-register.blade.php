<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        @if(!$submitted)
            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                    <h2 class="text-3xl font-bold text-white">Pendaftaran Penghuni Kos</h2>
                    <p class="text-blue-100 mt-2">Silakan lengkapi data diri Anda untuk mendaftar sebagai penghuni</p>
                </div>

                <!-- Error Message -->
                @if (session()->has('error'))
                    <div class="mx-8 mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Form -->
                <form wire:submit.prevent="submit" class="px-8 py-8">
                    <div class="space-y-6">
                        <!-- Section: Data Pribadi -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Data Pribadi</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Nama -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="nama" type="text" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="Masukkan nama lengkap Anda">
                                    @error('nama') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="email" type="email" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="contoh@email.com">
                                    @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- No HP -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Nomor HP <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="no_hp" type="text" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="08xxxxxxxxxx">
                                    @error('no_hp') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Alamat -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Alamat Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <textarea wire:model="alamat" rows="3" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="Masukkan alamat lengkap Anda"></textarea>
                                    @error('alamat') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section: Informasi Kos -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Kos</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Pilih Kamar -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Pilih Kamar (Opsional)
                                    </label>
                                    <select wire:model="room_id" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                        <option value="">Pilih Kamar</option>
                                        @foreach($rooms as $room)
                                            <option value="{{ $room->id }}">{{ $room->nama }} - {{ $room->tipe ?? '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('room_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika belum menentukan kamar</p>
                                </div>

                                <!-- Tanggal Masuk -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tanggal Masuk <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="tanggal_masuk" type="date" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        min="{{ date('Y-m-d') }}">
                                    @error('tanggal_masuk') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Catatan -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Catatan Tambahan (Opsional)
                                    </label>
                                    <textarea wire:model="catatan" rows="3" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="Tuliskan catatan atau permintaan khusus"></textarea>
                                    @error('catatan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section: Upload Dokumen -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Upload Dokumen</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Upload KTP -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Upload KTP <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                                    <span>Upload file</span>
                                                    <input wire:model="ktp_file" type="file" class="sr-only" accept="image/*,application/pdf">
                                                </label>
                                                <p class="pl-1">atau drag & drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, PDF (max. 2MB)</p>
                                        </div>
                                    </div>
                                    @if($ktp_file)
                                        <p class="text-sm text-green-600 mt-2">✓ {{ $ktp_file->getClientOriginalName() }}</p>
                                    @endif
                                    @error('ktp_file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="ktp_file" class="text-blue-500 text-sm mt-2">
                                        <svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Mengupload file...
                                    </div>
                                </div>

                                <!-- Upload Perjanjian -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Upload Perjanjian Kos (Opsional)
                                    </label>
                                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-400 transition">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                                    <span>Upload file</span>
                                                    <input wire:model="perjanjian_file" type="file" class="sr-only" accept="image/*,application/pdf">
                                                </label>
                                                <p class="pl-1">atau drag & drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PNG, JPG, PDF (max. 2MB)</p>
                                        </div>
                                    </div>
                                    @if($perjanjian_file)
                                        <p class="text-sm text-green-600 mt-2">✓ {{ $perjanjian_file->getClientOriginalName() }}</p>
                                    @endif
                                    @error('perjanjian_file') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="perjanjian_file" class="text-blue-500 text-sm mt-2">
                                        <svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Mengupload file...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">Informasi Penting</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Data yang Anda masukkan akan diverifikasi oleh pemilik kos</li>
                                            <li>Pastikan semua data yang diisi sudah benar dan lengkap</li>
                                            <li>Status pendaftaran Anda dapat dilihat melalui email yang terdaftar</li>
                                            <li>Proses verifikasi biasanya memakan waktu 1-3 hari kerja</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <button type="submit" 
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submit">Daftar Sekarang</span>
                                <span wire:loading wire:target="submit">
                                    <svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        @else
            <!-- Success Message -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="text-center px-8 py-12">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Pendaftaran Berhasil!</h3>
                    <p class="text-gray-600 mb-6">
                        Data Anda telah berhasil dikirim dan sedang menunggu verifikasi dari pemilik kos. 
                        Kami akan mengirimkan notifikasi ke email Anda setelah proses verifikasi selesai.
                    </p>
                    <div class="space-y-3">
                        <button wire:click="resetForm" 
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition">
                            Daftar Lagi
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Footer Info -->
        <div class="mt-6 text-center text-gray-600 text-sm">
            <p>Butuh bantuan? Hubungi kami di <a href="" class="text-blue-600 hover:underline"></a></p>
        </div>
    </div>
</div>