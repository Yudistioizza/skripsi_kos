<div class="flex h-screen bg-gray-50">
    {{-- Sidebar --}}
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">Manajemen Kamar</h1>
        </div>
        
        <nav class="flex-1 p-4 space-y-1">
            <button
                wire:click="$set('activeTab', 'rooms')"
                @class([
                    'w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all',
                    'bg-indigo-50 text-indigo-600' => $activeTab === 'rooms',
                    'text-gray-600 hover:bg-gray-50' => $activeTab !== 'rooms',
                ])>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Kamar</span>
            </button>

            <button
                wire:click="$set('activeTab', 'settings')"
                @class([
                    'w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all',
                    'bg-indigo-50 text-indigo-600' => $activeTab === 'settings',
                    'text-gray-600 hover:bg-gray-50' => $activeTab !== 'settings',
                ])>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Pengaturan</span>
            </button>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 overflow-auto">
        <div class="max-w-7xl mx-auto p-8">
            {{-- Alert Messages --}}
            @if (session()->has('message'))
                <div x-data x-init="setTimeout(() => $el.remove(), 4000)" class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-green-800">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div x-data x-init="setTimeout(() => $el.remove(), 4000)" class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Rooms Tab --}}
            @if ($activeTab === 'rooms')
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900">Manajemen Kamar</h2>
                        <button wire:click="openRoomForm"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Kamar
                        </button>
                    </div>

                    {{-- Statistics Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600 mb-1">Kamar Kosong</p>
                                    <p class="text-3xl font-bold text-green-900">{{ $stats['kosong'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-red-600 mb-1">Kamar Terisi</p>
                                    <p class="text-3xl font-bold text-red-900">{{ $stats['terisi'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-red-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-yellow-600 mb-1">Kamar Booking</p>
                                    <p class="text-3xl font-bold text-yellow-900">{{ $stats['booking'] }}</p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Section --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter Kamar</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Gedung</label>
                                <select wire:model.live="selectedBuilding"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    <option value="">Semua Gedung</option>
                                    @foreach($buildings as $building)
                                        <option value="{{ $building->id }}">{{ $building->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lantai</label>
                                <select wire:model.live="selectedFloor"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    <option value="">Semua Lantai</option>
                                    @foreach($floors as $floor)
                                        <option value="{{ $floor->id }}">Lantai {{ $floor->nomor_lantai }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Rooms Grid by Floor --}}
                        @if($selectedBuilding && $roomsByFloor)
                            <div class="mt-6 space-y-6">
                                @foreach($roomsByFloor as $floorNumber => $rooms)
                                    <div>
                                        <div class="flex items-center gap-2 mb-3">
                                            <div class="w-1 h-6 bg-indigo-600 rounded-full"></div>
                                            <h4 class="text-base font-semibold text-gray-800">Lantai {{ $floorNumber }}</h4>
                                            <span class="text-sm text-gray-500">({{ count($rooms) }} kamar)</span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-3">
                                            @foreach($rooms as $room)
                                                @php
                                                    $btnClass = match($room->status) {
                                                        'kosong'  => 'bg-green-50 hover:bg-green-100 text-green-700 border-green-200',
                                                        'terisi'  => 'bg-red-50 hover:bg-red-100 text-red-700 border-red-200',
                                                        'booking' => 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700 border-yellow-200',
                                                    };
                                                @endphp
                                                <button wire:click="showRoomDetail({{ $room->id }})"
                                                    class="px-4 py-3 rounded-lg border-2 text-sm font-semibold transition-all transform hover:scale-105 {{ $btnClass }}">
                                                    {{ $room->nomor_kamar }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Rooms Table --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomor Kamar</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Gedung</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Lantai</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tipe</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rooms as $room)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $room->nomor_kamar }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $room->building->nama }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $room->floor->nomor_lantai }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $room->roomType->nama }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @php
                                                    $badge = match($room->status) {
                                                        'kosong'  => 'bg-green-100 text-green-700',
                                                        'terisi'  => 'bg-red-100 text-red-700',
                                                        'booking' => 'bg-yellow-100 text-yellow-700',
                                                    };
                                                @endphp
                                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $badge }}">
                                                    {{ ucfirst($room->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                                <button wire:click="editRoom({{ $room->id }})"
                                                        class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                                                <button wire:click="deleteRoom({{ $room->id }})"
                                                        onclick="return confirm('Yakin ingin menghapus kamar ini?')"
                                                        class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Settings Tab --}}
            @if ($activeTab === 'settings')
                <div class="space-y-6">
                    <h2 class="text-2xl font-bold text-gray-900">Pengaturan</h2>

                    {{-- Buildings Section --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Gedung</h3>
                                <p class="text-sm text-gray-500 mt-1">Kelola data gedung</p>
                            </div>
                            <button wire:click="openBuildingForm"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Gedung
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Gedung</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Lantai</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Kamar</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($buildings as $building)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $building->nama }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $building->floors_count }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $building->rooms_count }}</td>
                                            <td class="px-6 py-4 text-sm space-x-3">
                                                <button wire:click="editBuilding({{ $building->id }})"
                                                        class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                                                <button wire:click="deleteBuilding({{ $building->id }})"
                                                        onclick="return confirm('Yakin ingin menghapus gedung ini?')"
                                                        class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Floors Section --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Lantai</h3>
                                <p class="text-sm text-gray-500 mt-1">Kelola data lantai per gedung</p>
                            </div>
                            <button wire:click="openFloorForm"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Lantai
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Gedung</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nomor Lantai</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Kamar</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($allFloors as $floor)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $floor->building->nama }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $floor->nomor_lantai }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $floor->rooms_count }}</td>
                                            <td class="px-6 py-4 text-sm space-x-3">
                                                <button wire:click="editFloor({{ $floor->id }})"
                                                        class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                                                <button wire:click="deleteFloor({{ $floor->id }})"
                                                        onclick="return confirm('Yakin ingin menghapus lantai ini?')"
                                                        class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Room Types Section --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Tipe Kamar</h3>
                                <p class="text-sm text-gray-500 mt-1">Kelola tipe dan harga kamar</p>
                            </div>
                            <button wire:click="openRoomTypeForm"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Tipe
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Tipe</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jumlah Kamar</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($roomTypes as $type)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $type->nama }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold">Rp {{ number_format($type->harga, 0, ',', '.') }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $type->rooms_count }}</td>
                                            <td class="px-6 py-4 text-sm space-x-3">
                                                <button wire:click="editRoomType({{ $type->id }})"
                                                        class="text-indigo-600 hover:text-indigo-900 font-medium">Edit</button>
                                                <button wire:click="deleteRoomType({{ $type->id }})"
                                                        onclick="return confirm('Yakin ingin menghapus tipe ini?')"
                                                        class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </main>

    {{-- Modal Forms --}}
    <flux:modal wire:model="showBuildingForm" heading="Form Gedung">
        <div class="space-y-4">
            <flux:input wire:model="building_nama" label="Nama Gedung" />
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showBuildingForm', false)">Batal</flux:button>
                <flux:button wire:click="saveBuilding">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="showFloorForm" heading="Form Lantai">
        <div class="space-y-4">
            <div>
                <label for="floor_building_id" class="block text-sm font-medium text-gray-700 mb-1">Gedung</label>
                <select id="floor_building_id" wire:model="floor_building_id"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Pilih Gedung</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}">{{ $building->nama }}</option>
                    @endforeach
                </select>
            </div>
            <flux:input type="number" wire:model="floor_nomor_lantai" label="Nomor Lantai" />
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showFloorForm', false)">Batal</flux:button>
                <flux:button wire:click="saveFloor">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="showRoomTypeForm" heading="Form Tipe Kamar">
        <div class="space-y-4">
            <flux:input wire:model="roomtype_nama" label="Nama Tipe" />
            <flux:input type="number" wire:model="roomtype_harga" label="Harga" />
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" wire:click="$set('showRoomTypeForm', false)">Batal</flux:button>
                <flux:button wire:click="saveRoomType">Simpan</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal wire:model="showRoomForm" heading="Form Kamar">
        <div class="space-y-4">
            <!-- Gedung -->
            <div>
                <label for="room_building_id" class="block text-sm font-medium text-gray-700 mb-2">Gedung</label>
                <select id="room_building_id" wire:model="room_building_id"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="">Pilih Gedung</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}">{{ $building->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Lantai -->
            <div>
                <label for="room_floor_id" class="block text-sm font-medium text-gray-700 mb-2">Lantai</label>
                <select id="room_floor_id" wire:model="room_floor_id"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="">Pilih Lantai</option>
                    @foreach($floorsForRoom as $floor)
                        <option value="{{ $floor->id }}">Lantai {{ $floor->nomor_lantai }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Tipe Kamar -->
            <div>
                <label for="room_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kamar</label>
                <select id="room_type_id" wire:model="room_type_id"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="">Pilih Tipe</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->nama }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nomor Kamar -->
            <div>
                <label for="nomor_kamar" class="block text-sm font-medium text-gray-700 mb-2">Nomor Kamar</label>
                <input id="nomor_kamar" type="text" wire:model="nomor_kamar"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" wire:model="status"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="kosong">Kosong</option>
                    <option value="terisi">Terisi</option>
                    <option value="booking">Booking</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 pt-4">
                <button wire:click="$set('showRoomForm', false)"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button wire:click="saveRoom"
                    class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm transition-all">
                    Simpan
                </button>
            </div>
        </div>
    </flux:modal>

    {{-- Room Detail Modal --}}
    @if($showRoomModal && $selectedRoom)
        <flux:modal wire:model="showRoomModal" heading="Detail Kamar">
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Nomor Kamar</span>
                        <span class="text-lg font-bold text-gray-900">{{ $selectedRoom->nomor_kamar }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Gedung</span>
                        <span class="text-sm text-gray-900">{{ $selectedRoom->building->nama }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Lantai</span>
                        <span class="text-sm text-gray-900">{{ $selectedRoom->floor->nomor_lantai }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Tipe</span>
                        <span class="text-sm text-gray-900">{{ $selectedRoom->roomType->nama }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Harga</span>
                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($selectedRoom->roomType->harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Status</span>
                        <flux:badge :color="$selectedRoom->status === 'kosong' ? 'green' : ($selectedRoom->status === 'terisi' ? 'red' : 'yellow')">
                            {{ ucfirst($selectedRoom->status) }}
                        </flux:badge>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Ubah Status Kamar</p>
                    <div class="flex gap-2">
                        <button wire:click="updateRoomStatus({{ $selectedRoom->id }}, 'kosong')"
                                class="flex-1 px-4 py-2.5 bg-green-50 text-green-700 border-2 border-green-200 rounded-lg text-sm font-medium hover:bg-green-100 transition-all">
                            Kosong
                        </button>
                        <button wire:click="updateRoomStatus({{ $selectedRoom->id }}, 'booking')"
                                class="flex-1 px-4 py-2.5 bg-yellow-50 text-yellow-700 border-2 border-yellow-200 rounded-lg text-sm font-medium hover:bg-yellow-100 transition-all">
                            Booking
                        </button>
                        <button wire:click="updateRoomStatus({{ $selectedRoom->id }}, 'terisi')"
                                class="flex-1 px-4 py-2.5 bg-red-50 text-red-700 border-2 border-red-200 rounded-lg text-sm font-medium hover:bg-red-100 transition-all">
                            Terisi
                        </button>
                    </div>
                </div>
            </div>
        </flux:modal>
    @endif
</div>