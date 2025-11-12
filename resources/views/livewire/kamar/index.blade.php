<div class="min-h-screen">
    <header class="bg-white border-b border-gray-200 top-0 z-10">
        <div class="mx-auto px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Kamar</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola kamar, gedung, lantai, dan tipe kamar</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="$set('showSettingsModal', true)"
                    class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Pengaturan
                </button>
                <button wire:click="openRoomForm"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Kamar
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto px-6 py-8 space-y-6">
        {{-- Alert Messages --}}
        @if (session()->has('message'))
            <div x-data x-init="setTimeout(() => $el.remove(), 4000)" class="p-4 rounded-lg bg-green-50 border border-green-200">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium text-green-800">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data x-init="setTimeout(() => $el.remove(), 4000)" class="p-4 rounded-lg bg-red-50 border border-red-200">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 mb-1">Kamar Kosong</p>
                        <p class="text-3xl font-bold text-green-900">{{ $stats['kosong'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                        <svg class="w-6 h-6 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
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
                        <svg class="w-6 h-6 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
                    <select wire:model.live="selectedBuilding" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Semua Gedung</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lantai</label>
                    <select wire:model.live="selectedFloor" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Semua Lantai</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor->id }}">Lantai {{ $floor->nomor_lantai }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

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
                                    <button wire:click="showRoomDetail({{ $room->id }})" class="px-4 py-3 rounded-lg border-2 text-sm font-semibold transition-all transform hover:scale-105 {{ $btnClass }}">
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
                                            class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="openDeleteModal('room', {{ $room->id }}, 'Kamar {{ $room->nomor_kamar }}')" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- Modal Settings --}}
    <flux:modal wire:model="showSettingsModal" heading="Pengaturan Kamar">
        <div class="space-y-6">
            {{-- Buildings Section --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-800">Gedung</h4>
                    <button wire:click="openBuildingForm" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">+ Tambah</button>
                </div>
                <ul class="space-y-2">
                    @foreach($buildings as $building)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $building->nama }}</span>
                            <div class="flex gap-2">
                                <button wire:click="editBuilding({{ $building->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="openDeleteModal('building', {{ $building->id }}, '{{ $building->nama }}')" class="text-red-600 hover:text-red-900">Hapus</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Floors Section --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-800">Lantai</h4>
                    <button wire:click="openFloorForm" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">+ Tambah</button>
                </div>
                <ul class="space-y-2">
                    @foreach($allFloors as $floor)
                        <li class="flex items-center justify-between text-sm">
                            <span>Lantai {{ $floor->nomor_lantai }} - {{ $floor->building->nama }}</span>
                            <div class="flex gap-2">
                                <button wire:click="editFloor({{ $floor->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="openDeleteModal('floor', {{ $floor->id }}, 'Lantai {{ $floor->nomor_lantai }} - {{ $floor->building->nama }}')" class="text-red-600 hover:text-red-900">Hapus</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Room Types Section --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-800">Tipe Kamar</h4>
                    <button wire:click="openRoomTypeForm" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">+ Tambah</button>
                </div>
                <ul class="space-y-2">
                    @foreach($roomTypes as $type)
                        <li class="flex items-center justify-between text-sm">
                            <span>{{ $type->nama }} - Rp {{ number_format($type->harga, 0, ',', '.') }}</span>
                            <div class="flex gap-2">
                                <button wire:click="editRoomType({{ $type->id }})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button wire:click="openDeleteModal('roomtype', {{ $type->id }}, '{{ $type->nama }}')" class="text-red-600 hover:text-red-900">Hapus</button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Forms for CRUD --}}
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
                <select id="floor_building_id" wire:model="floor_building_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
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

    {{-- Modal Room Form --}}
    <flux:modal wire:model="showRoomForm" heading="Form Kamar">
        <div class="space-y-4">
            <div>
                <label for="room_building_id" class="block text-sm font-medium text-gray-700 mb-2">Gedung</label>
                <select id="room_building_id" wire:model.live="room_building_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="">Pilih Gedung</option>
                    @foreach($buildings as $building)
                        <option value="{{ $building->id }}">{{ $building->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="room_floor_id" class="block text-sm font-medium text-gray-700 mb-2">Lantai</label>
                <select id="room_floor_id" wire:model="room_floor_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="">Pilih Lantai</option>
                    @foreach($floorsForRoom as $floor)
                        <option value="{{ $floor->id }}">Lantai {{ $floor->nomor_lantai }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="room_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipe Kamar</label>
                <select id="room_type_id" wire:model="room_type_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="">Pilih Tipe</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="nomor_kamar" class="block text-sm font-medium text-gray-700 mb-2">Nomor Kamar</label>
                <input id="nomor_kamar" type="text" wire:model="nomor_kamar" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" wire:model="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="kosong">Kosong</option>
                    <option value="terisi">Terisi</option>
                    <option value="booking">Booking</option>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-4">
                <button wire:click="$set('showRoomForm', false)" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">Batal</button>
                <button wire:click="saveRoom" class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 shadow-sm transition-all">Simpan</button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal Room Detail --}}
    @if($showRoomModal && $selectedRoom)
        <flux:modal wire:model="showRoomModal" heading="Detail Kamar">
            <div class="space-y-4">
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between items-center"><span class="text-sm font-medium text-gray-600">Nomor Kamar</span><span class="text-lg font-bold text-gray-900">{{ $selectedRoom->nomor_kamar }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-sm font-medium text-gray-600">Gedung</span><span class="text-sm text-gray-900">{{ $selectedRoom->building->nama }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-sm font-medium text-gray-600">Lantai</span><span class="text-sm text-gray-900">Lantai {{ $selectedRoom->floor->nomor_lantai }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-sm font-medium text-gray-600">Tipe</span><span class="text-sm text-gray-900">{{ $selectedRoom->roomType->nama }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-sm font-medium text-gray-600">Harga</span><span class="text-sm font-bold text-gray-900">Rp {{ number_format($selectedRoom->roomType->harga, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between items-center"><span class="text-sm font-medium text-gray-600">Status</span>
                        <flux:badge :color="$selectedRoom->status === 'kosong' ? 'green' : ($selectedRoom->status === 'terisi' ? 'red' : 'yellow')">{{ ucfirst($selectedRoom->status) }}</flux:badge>
                    </div>
                </div>
                <div class="border-t pt-4">
                    <p class="text-sm font-medium text-gray-700 mb-3">Ubah Status Kamar</p>
                    <div class="flex gap-2">
                        <button wire:click="updateRoomStatus({{ $selectedRoom->id }}, 'kosong')" class="flex-1 px-4 py-2.5 bg-green-50 text-green-700 border-2 border-green-200 rounded-lg text-sm font-medium hover:bg-green-100 transition-all">Kosong</button>
                        <button wire:click="updateRoomStatus({{ $selectedRoom->id }}, 'booking')" class="flex-1 px-4 py-2.5 bg-yellow-50 text-yellow-700 border-2 border-yellow-200 rounded-lg text-sm font-medium hover:bg-yellow-100 transition-all">Booking</button>
                        <button wire:click="updateRoomStatus({{ $selectedRoom->id }}, 'terisi')" class="flex-1 px-4 py-2.5 bg-red-50 text-red-700 border-2 border-red-200 rounded-lg text-sm font-medium hover:bg-red-100 transition-all">Terisi</button>
                    </div>
                </div>
            </div>
        </flux:modal>
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