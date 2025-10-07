<?php

namespace App\Livewire\Kamar;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use Livewire\Component;

class Index extends Component
{
    // Tabs
    public $activeTab = 'rooms';

    // Filters
    public $selectedBuilding = null;
    public $selectedFloor = null;
    public $floors = [];

    // Forms
    public $showBuildingForm = false;
    public $showFloorForm = false;
    public $showRoomTypeForm = false;
    public $showRoomForm = false;
    public $showRoomModal = false;

    // Building Form
    public $building_id = '';
    public $building_nama = '';
    public $editingBuildingId = null;

    // Floor Form
    public $floor_building_id = '';
    public $floor_nomor_lantai = '';
    public $editingFloorId = null;

    // Room Type Form
    public $roomtype_nama = '';
    public $roomtype_harga = '';
    public $editingRoomTypeId = null;

    // Room Form
    public $room_building_id = '';
    public $room_floor_id = '';
    public $room_type_id = '';
    public $nomor_kamar = '';
    public $status = 'kosong';
    public $editingRoomId = null;
    public $floorsForRoom = [];
    public bool $showSettingsModal = false;


    // Selected Room Detail
    public $selectedRoom = null;

    protected function rules()
    {
        return [
            'building_nama' => 'required|string|max:255',
            'floor_building_id' => 'required|exists:buildings,id',
            'floor_nomor_lantai' => 'required|integer|min:1',
            'roomtype_nama' => 'required|string|max:255',
            'roomtype_harga' => 'required|numeric|min:0',
            'room_building_id' => 'required|exists:buildings,id',
            'room_floor_id' => 'required|exists:floors,id',
            'room_type_id' => 'required|exists:room_types,id',
            'nomor_kamar' => 'required|string|max:50',
            'status' => 'required|in:kosong,terisi,booking',
        ];
    }

    public function mount()
    {
        $this->loadFloors();
    }

    public function updatedSelectedBuilding($value)
    {
        $this->selectedFloor = null;
        $this->loadFloors();
    }

    public function updatedRoom_building_id($value)
    {
        $this->room_floor_id = '';
        $this->loadFloorsForRoom($value);
    }

    public function loadFloors()
    {
        if ($this->selectedBuilding) {
            $this->floors = Floor::where('building_id', $this->selectedBuilding)
                ->orderBy('nomor_lantai')
                ->get();
        } else {
            $this->floors = Floor::orderBy('nomor_lantai')->get();
        }
    }

    public function loadFloorsForRoom($buildingId)
    {
        if ($buildingId) {
            $this->floorsForRoom = Floor::where('building_id', $buildingId)->get();
        } else {
            $this->floorsForRoom = [];
        }
    }

    public function getStatsProperty()
    {
        $query = Room::query();

        if ($this->selectedBuilding) {
            $query->where('building_id', $this->selectedBuilding);
        }

        if ($this->selectedFloor) {
            $query->where('floor_id', $this->selectedFloor);
        }

        return [
            'kosong' => $query->clone()->where('status', 'kosong')->count(),
            'terisi' => $query->clone()->where('status', 'terisi')->count(),
            'booking' => $query->clone()->where('status', 'booking')->count(),
        ];
    }

    // Building Methods
    public function openBuildingForm()
    {
        $this->resetBuildingForm();
        $this->showBuildingForm = true;
    }

    public function resetBuildingForm()
    {
        $this->building_nama = '';
        $this->editingBuildingId = null;
        $this->resetValidation(['building_nama']);
    }

    public function saveBuilding()
    {
        $this->validate(['building_nama' => 'required|string|max:255']);

        if ($this->editingBuildingId) {
            Building::find($this->editingBuildingId)->update(['nama' => $this->building_nama]);
            session()->flash('message', 'Gedung berhasil diupdate!');
        } else {
            Building::create(['nama' => $this->building_nama]);
            session()->flash('message', 'Gedung berhasil ditambahkan!');
        }

        $this->showBuildingForm = false;
        $this->resetBuildingForm();
    }

    public function editBuilding($id)
    {
        $building = Building::find($id);
        $this->editingBuildingId = $id;
        $this->building_nama = $building->nama;
        $this->showBuildingForm = true;
    }

    public function deleteBuilding($id)
    {
        $building = Building::find($id);
        if ($building->rooms()->count() > 0) {
            session()->flash('error', 'Tidak dapat menghapus gedung yang masih memiliki kamar!');
            return;
        }
        $building->delete();
        session()->flash('message', 'Gedung berhasil dihapus!');
    }

    // Floor Methods
    public function openFloorForm()
    {
        $this->resetFloorForm();
        $this->showFloorForm = true;
    }

    public function resetFloorForm()
    {
        $this->floor_building_id = '';
        $this->floor_nomor_lantai = '';
        $this->editingFloorId = null;
        $this->resetValidation(['floor_building_id', 'floor_nomor_lantai']);
    }

    public function saveFloor()
    {
        $this->validate([
            'floor_building_id' => 'required|exists:buildings,id',
            'floor_nomor_lantai' => 'required|integer|min:1',
        ]);

        if ($this->editingFloorId) {
            Floor::find($this->editingFloorId)->update([
                'building_id' => $this->floor_building_id,
                'nomor_lantai' => $this->floor_nomor_lantai,
            ]);
            session()->flash('message', 'Lantai berhasil diupdate!');
        } else {
            Floor::create([
                'building_id' => $this->floor_building_id,
                'nomor_lantai' => $this->floor_nomor_lantai,
            ]);
            session()->flash('message', 'Lantai berhasil ditambahkan!');
        }

        $this->showFloorForm = false;
        $this->resetFloorForm();
        $this->loadFloors();
    }

    public function editFloor($id)
    {
        $floor = Floor::find($id);
        $this->editingFloorId = $id;
        $this->floor_building_id = $floor->building_id;
        $this->floor_nomor_lantai = $floor->nomor_lantai;
        $this->showFloorForm = true;
    }

    public function deleteFloor($id)
    {
        $floor = Floor::find($id);
        if ($floor->rooms()->count() > 0) {
            session()->flash('error', 'Tidak dapat menghapus lantai yang masih memiliki kamar!');
            return;
        }
        $floor->delete();
        session()->flash('message', 'Lantai berhasil dihapus!');
        $this->loadFloors();
    }

    // Room Type Methods
    public function openRoomTypeForm()
    {
        $this->resetRoomTypeForm();
        $this->showRoomTypeForm = true;
    }

    public function resetRoomTypeForm()
    {
        $this->roomtype_nama = '';
        $this->roomtype_harga = '';
        $this->editingRoomTypeId = null;
        $this->resetValidation(['roomtype_nama', 'roomtype_harga']);
    }

    public function saveRoomType()
    {
        $this->validate([
            'roomtype_nama' => 'required|string|max:255',
            'roomtype_harga' => 'required|numeric|min:0',
        ]);

        if ($this->editingRoomTypeId) {
            RoomType::find($this->editingRoomTypeId)->update([
                'nama' => $this->roomtype_nama,
                'harga' => $this->roomtype_harga,
            ]);
            session()->flash('message', 'Tipe kamar berhasil diupdate!');
        } else {
            RoomType::create([
                'nama' => $this->roomtype_nama,
                'harga' => $this->roomtype_harga,
            ]);
            session()->flash('message', 'Tipe kamar berhasil ditambahkan!');
        }

        $this->showRoomTypeForm = false;
        $this->resetRoomTypeForm();
    }

    public function editRoomType($id)
    {
        $roomType = RoomType::find($id);
        $this->editingRoomTypeId = $id;
        $this->roomtype_nama = $roomType->nama;
        $this->roomtype_harga = $roomType->harga;
        $this->showRoomTypeForm = true;
    }

    public function deleteRoomType($id)
    {
        $roomType = RoomType::find($id);
        if ($roomType->rooms()->count() > 0) {
            session()->flash('error', 'Tidak dapat menghapus tipe kamar yang masih digunakan!');
            return;
        }
        $roomType->delete();
        session()->flash('message', 'Tipe kamar berhasil dihapus!');
    }

    // Room Methods
    public function openRoomForm()
    {
        $this->resetRoomForm();
        $this->showRoomForm = true;
    }

    public function resetRoomForm()
    {
        $this->room_building_id = '';
        $this->room_floor_id = '';
        $this->room_type_id = '';
        $this->nomor_kamar = '';
        $this->status = 'kosong';
        $this->floorsForRoom = [];
        $this->editingRoomId = null;
        $this->resetValidation(['room_building_id', 'room_floor_id', 'room_type_id', 'nomor_kamar', 'status']);
    }

    public function saveRoom()
    {
        $this->validate([
            'room_building_id' => 'required|exists:buildings,id',
            'room_floor_id' => 'required|exists:floors,id',
            'room_type_id' => 'required|exists:room_types,id',
            'nomor_kamar' => 'required|string|max:50',
            'status' => 'required|in:kosong,terisi,booking',
        ]);

        if ($this->editingRoomId) {
            Room::find($this->editingRoomId)->update([
                'building_id' => $this->room_building_id,
                'floor_id' => $this->room_floor_id,
                'room_type_id' => $this->room_type_id,
                'nomor_kamar' => $this->nomor_kamar,
                'status' => $this->status,
            ]);
            session()->flash('message', 'Kamar berhasil diupdate!');
        } else {
            Room::create([
                'building_id' => $this->room_building_id,
                'floor_id' => $this->room_floor_id,
                'room_type_id' => $this->room_type_id,
                'nomor_kamar' => $this->nomor_kamar,
                'status' => $this->status,
            ]);
            session()->flash('message', 'Kamar berhasil ditambahkan!');
        }

        $this->showRoomForm = false;
        $this->resetRoomForm();
    }

    public function editRoom($id)
    {
        $room = Room::find($id);
        $this->editingRoomId = $id;
        $this->room_building_id = $room->building_id;
        $this->loadFloorsForRoom($room->building_id);
        $this->room_floor_id = $room->floor_id;
        $this->room_type_id = $room->room_type_id;
        $this->nomor_kamar = $room->nomor_kamar;
        $this->status = $room->status;
        $this->showRoomForm = true;
    }

    public function deleteRoom($id)
    {
        Room::find($id)->delete();
        session()->flash('message', 'Kamar berhasil dihapus!');
    }

    public function showRoomDetail($roomId)
    {
        $this->selectedRoom = Room::with(['building', 'floor', 'roomType'])->find($roomId);
        $this->showRoomModal = true;
    }

    public function updateRoomStatus($roomId, $status)
    {
        $room = Room::find($roomId);
        if ($room) {
            $room->update(['status' => $status]);
            $this->selectedRoom = Room::with(['building', 'floor', 'roomType'])->find($roomId);
        }
    }

    /**
     * Kelompokkan kamar per lantai untuk gedung yang dipilih.
     * @return \Illuminate\Support\Collection|array
     */
    public function getRoomsByFloorProperty()
    {
        if (!$this->selectedBuilding) {
            return [];
        }

        return Room::whereHas('floor', fn($q) => $q->where('building_id', $this->selectedBuilding))
            ->with(['floor', 'roomType'])
            ->get()
            ->groupBy('floor.nomor_lantai');
    }

    public function render()
    {
        $buildings = Building::withCount(['rooms', 'floors'])->get();
        if ($this->room_building_id && empty($this->floorsForRoom)) {
            $this->loadFloorsForRoom($this->room_building_id);
        }
        $allFloors = Floor::with('building')->withCount('rooms')->orderBy('building_id')->orderBy('nomor_lantai')->get();
        $roomTypes = RoomType::withCount('rooms')->get();
        $rooms = Room::with(['building', 'floor', 'roomType'])
            ->orderBy('building_id')
            ->orderBy('floor_id')
            ->orderBy('nomor_kamar')
            ->get();

        // For grid view
        $roomsByFloor = [];
        if ($this->selectedBuilding) {
            $building = Building::with(['floors.rooms.roomType'])->find($this->selectedBuilding);
            if ($building) {
                foreach ($building->floors as $floor) {
                    $roomsByFloor[$floor->nomor_lantai] = $floor->rooms;
                }
            }
        }

        return view('livewire.kamar.index', [
            'buildings' => $buildings,
            'allFloors' => $allFloors,
            'roomTypes' => $roomTypes,
            'rooms' => $rooms,
            'roomsByFloor' => $roomsByFloor,   // <-- pakai array ini
            'stats' => $this->stats,
        ]);
    }
}