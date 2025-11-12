<?php

namespace App\Livewire\Kamar;

use App\Models\Building;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use Livewire\Component;

class Index extends Component
{
    // Constants
    private const DELETE_TYPE_BUILDING = 'building';
    private const DELETE_TYPE_FLOOR = 'floor';
    private const DELETE_TYPE_ROOM_TYPE = 'roomtype';
    private const DELETE_TYPE_ROOM = 'room';

    public const STATUS_KOSONG = 'kosong';
    public const STATUS_TERISI = 'terisi';
    public const STATUS_BOOKING = 'booking';

    // Properties
    public $activeTab = 'rooms';
    public $selectedBuilding = null;
    public $selectedFloor = null;
    public $floors = [];

    // Form States
    public $showBuildingForm = false, $showFloorForm = false;
    public $showRoomTypeForm = false, $showRoomForm = false;
    public $showRoomModal = false, $showDeleteModal = false;

    // Entity Fields
    public $building_id = '', $building_nama = '', $editingBuildingId = null;
    public $floor_building_id = '', $floor_nomor_lantai = '', $editingFloorId = null;
    public $roomtype_nama = '', $roomtype_harga = '', $editingRoomTypeId = null;
    public $room_building_id = '', $room_floor_id = '', $room_type_id = '';
    public $nomor_kamar = '', $status = self::STATUS_KOSONG, $editingRoomId = null;
    public $floorsForRoom = [], $selectedRoom = null;

    // Delete Modal
    public $deleteItemName = '', $deleteId = null, $deleteType = '';

    // Lifecycle
    public function mount(): void
    {
        $this->loadFloors();
    }

    // Event Handlers
    public function updatedSelectedBuilding(): void
    {
        $this->selectedFloor = null;
        $this->loadFloors();
    }

    public function updatedRoom_building_id($value): void
    {
        $this->loadFloorsForRoom($value);
    }

    // Delete Operations
    public function openDeleteModal($type, $id, $name): void
    {
        $this->deleteType = $type;
        $this->deleteId = $id;
        $this->deleteItemName = $name;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->reset(['deleteType', 'deleteId', 'deleteItemName', 'showDeleteModal']);
    }

    public function confirmDelete(): void
    {
        try {
            $this->performDeletion();
            $this->closeDeleteModal();
            $this->flashSuccess('Data berhasil dihapus!');
        } catch (\Exception $e) {
            $this->closeDeleteModal();
            $this->flashError($e->getMessage());
        }
    }

    private function performDeletion(): void
    {
        match ($this->deleteType) {
            self::DELETE_TYPE_BUILDING => $this->deleteBuilding($this->deleteId),
            self::DELETE_TYPE_FLOOR => $this->deleteFloor($this->deleteId),
            self::DELETE_TYPE_ROOM_TYPE => $this->deleteRoomType($this->deleteId),
            self::DELETE_TYPE_ROOM => $this->deleteRoom($this->deleteId),
            default => throw new \InvalidArgumentException('Tipe penghapusan tidak valid'),
        };
    }

    private function deleteBuilding($id): void
    {
        $building = $this->findOrFail(Building::class, $id);

        if ($building->rooms()->exists()) {
            throw new \Exception('Tidak dapat menghapus gedung yang masih memiliki kamar!');
        }

        $building->delete();
    }

    private function deleteFloor($id): void
    {
        $floor = $this->findOrFail(Floor::class, $id);

        if ($floor->rooms()->exists()) {
            throw new \Exception('Tidak dapat menghapus lantai yang masih memiliki kamar!');
        }

        $floor->delete();
        $this->loadFloors();
    }

    private function deleteRoomType($id): void
    {
        $roomType = $this->findOrFail(RoomType::class, $id);

        if ($roomType->rooms()->exists()) {
            throw new \Exception('Tidak dapat menghapus tipe kamar yang masih digunakan!');
        }

        $roomType->delete();
    }

    private function deleteRoom($id): void
    {
        $this->findOrFail(Room::class, $id)->delete();
    }

    // Building CRUD
    public function openBuildingForm(): void
    {
        $this->resetBuildingForm();
        $this->showBuildingForm = true;
    }

    public function saveBuilding(): void
    {
        $this->validateBuildingData();
        $this->preventDuplicateBuilding();
        $this->persistBuilding();
        $this->handleSaveSuccess('Gedung');
        $this->showBuildingForm = false;
    }

    public function editBuilding($id): void
    {
        $building = $this->findOrFail(Building::class, $id);

        $this->editingBuildingId = $id;
        $this->building_nama = $building->nama;
        $this->showBuildingForm = true;
    }

    private function resetBuildingForm(): void
    {
        $this->reset(['building_nama', 'editingBuildingId']);
        $this->resetValidation(['building_nama']);
    }

    private function validateBuildingData(): void
    {
        $this->validate(['building_nama' => $this->rules()['building_nama']]);
    }

    private function preventDuplicateBuilding(): void
    {
        if ($this->isDuplicateBuilding()) {
            $this->flashError('Nama gedung sudah ada.');
            abort(422);
        }
    }

    private function persistBuilding(): void
    {
        $data = ['nama' => $this->building_nama];

        $this->editingBuildingId
            ? $this->findOrFail(Building::class, $this->editingBuildingId)->update($data)
            : Building::create($data);
    }

    private function isDuplicateBuilding(): bool
    {
        return Building::whereRaw('LOWER(nama) = ?', [strtolower($this->building_nama)])
            ->when($this->editingBuildingId, fn($q) => $q->where('id', '!=', $this->editingBuildingId))
            ->exists();
    }

    // Floor CRUD
    public function openFloorForm(): void
    {
        $this->resetFloorForm();
        $this->showFloorForm = true;
    }

    public function saveFloor(): void
    {
        $this->validateFloorData();
        $this->preventDuplicateFloor();
        $this->persistFloor();
        $this->handleSaveSuccess('Lantai');
        $this->showFloorForm = false;
    }

    public function editFloor($id): void
    {
        $floor = $this->findOrFail(Floor::class, $id);

        $this->editingFloorId = $id;
        $this->floor_building_id = $floor->building_id;
        $this->floor_nomor_lantai = $floor->nomor_lantai;
        $this->showFloorForm = true;
    }

    private function resetFloorForm(): void
    {
        $this->reset(['floor_building_id', 'floor_nomor_lantai', 'editingFloorId']);
        $this->resetValidation(['floor_building_id', 'floor_nomor_lantai']);
    }

    private function validateFloorData(): void
    {
        $this->validate([
            'floor_building_id' => $this->rules()['floor_building_id'],
            'floor_nomor_lantai' => $this->rules()['floor_nomor_lantai'],
        ]);
    }

    private function preventDuplicateFloor(): void
    {
        if ($this->isDuplicateFloor()) {
            $this->flashError('Nomor lantai sudah ada di gedung ini.');
            abort(422);
        }
    }

    private function persistFloor(): void
    {
        $data = [
            'building_id' => $this->floor_building_id,
            'nomor_lantai' => $this->floor_nomor_lantai,
        ];

        $this->editingFloorId
            ? $this->findOrFail(Floor::class, $this->editingFloorId)->update($data)
            : Floor::create($data);

        $this->loadFloors();
    }

    private function isDuplicateFloor(): bool
    {
        return Floor::where('building_id', $this->floor_building_id)
            ->where('nomor_lantai', $this->floor_nomor_lantai)
            ->when($this->editingFloorId, fn($q) => $q->where('id', '!=', $this->editingFloorId))
            ->exists();
    }

    // RoomType CRUD
    public function openRoomTypeForm(): void
    {
        $this->resetRoomTypeForm();
        $this->showRoomTypeForm = true;
    }

    public function saveRoomType(): void
    {
        $this->validateRoomTypeData();
        $this->preventDuplicateRoomType();
        $this->persistRoomType();
        $this->handleSaveSuccess('Tipe kamar');
        $this->showRoomTypeForm = false;
    }

    public function editRoomType($id): void
    {
        $roomType = $this->findOrFail(RoomType::class, $id);

        $this->editingRoomTypeId = $id;
        $this->roomtype_nama = $roomType->nama;
        $this->roomtype_harga = $roomType->harga;
        $this->showRoomTypeForm = true;
    }

    private function resetRoomTypeForm(): void
    {
        $this->reset(['roomtype_nama', 'roomtype_harga', 'editingRoomTypeId']);
        $this->resetValidation(['roomtype_nama', 'roomtype_harga']);
    }

    private function validateRoomTypeData(): void
    {
        $this->validate([
            'roomtype_nama' => $this->rules()['roomtype_nama'],
            'roomtype_harga' => $this->rules()['roomtype_harga'],
        ]);
    }

    private function preventDuplicateRoomType(): void
    {
        if ($this->isDuplicateRoomType()) {
            $this->flashError('Nama tipe kamar sudah ada.');
            abort(422);
        }
    }

    private function persistRoomType(): void
    {
        $data = [
            'nama' => $this->roomtype_nama,
            'harga' => $this->roomtype_harga,
        ];

        $this->editingRoomTypeId
            ? $this->findOrFail(RoomType::class, $this->editingRoomTypeId)->update($data)
            : RoomType::create($data);
    }

    private function isDuplicateRoomType(): bool
    {
        return RoomType::whereRaw('LOWER(nama) = ?', [strtolower($this->roomtype_nama)])
            ->when($this->editingRoomTypeId, fn($q) => $q->where('id', '!=', $this->editingRoomTypeId))
            ->exists();
    }

    // Room CRUD
    public function openRoomForm(): void
    {
        $this->resetRoomForm();
        $this->showRoomForm = true;
    }

    public function saveRoom(): void
    {
        $this->validateRoomData();
        $this->preventDuplicateRoom();
        $this->persistRoom();
        $this->handleSaveSuccess('Kamar');
        $this->showRoomForm = false;
    }

    public function editRoom($id): void
    {
        $room = $this->findOrFail(Room::class, $id);

        $this->editingRoomId = $id;
        $this->room_building_id = $room->building_id;
        $this->loadFloorsForRoom($room->building_id);
        $this->room_floor_id = $room->floor_id;
        $this->room_type_id = $room->room_type_id;
        $this->nomor_kamar = $room->nomor_kamar;
        $this->status = $room->status;
        $this->showRoomForm = true;
    }

    private function resetRoomForm(): void
    {
        $this->reset([
            'room_building_id',
            'room_floor_id',
            'room_type_id',
            'nomor_kamar',
            'status',
            'editingRoomId',
            'floorsForRoom'
        ]);
        $this->resetValidation([
            'room_building_id',
            'room_floor_id',
            'room_type_id',
            'nomor_kamar',
            'status'
        ]);
    }

    private function validateRoomData(): void
    {
        $this->validate([
            'room_building_id' => $this->rules()['room_building_id'],
            'room_floor_id' => $this->rules()['room_floor_id'],
            'room_type_id' => $this->rules()['room_type_id'],
            'nomor_kamar' => $this->rules()['nomor_kamar'],
            'status' => $this->rules()['status'],
        ]);
    }

    private function preventDuplicateRoom(): void
    {
        if ($this->isDuplicateRoom()) {
            $this->flashError('Nomor kamar sudah ada di lantai ini.');
            abort(422);
        }
    }

    private function persistRoom(): void
    {
        $data = [
            'building_id' => $this->room_building_id,
            'floor_id' => $this->room_floor_id,
            'room_type_id' => $this->room_type_id,
            'nomor_kamar' => $this->nomor_kamar,
            'status' => $this->status,
        ];

        $this->editingRoomId
            ? $this->findOrFail(Room::class, $this->editingRoomId)->update($data)
            : Room::create($data);
    }

    private function isDuplicateRoom(): bool
    {
        return Room::where('floor_id', $this->room_floor_id)
            ->where('nomor_kamar', $this->nomor_kamar)
            ->when($this->editingRoomId, fn($q) => $q->where('id', '!=', $this->editingRoomId))
            ->exists();
    }

    // Room Detail
    public function showRoomDetail($roomId): void
    {
        $this->selectedRoom = $this->findOrFail(Room::class, $roomId, ['building', 'floor', 'roomType']);
        $this->showRoomModal = true;
    }

    public function updateRoomStatus($roomId, $status): void
    {
        Room::where('id', $roomId)->update(['status' => $status]);
        $this->selectedRoom = $this->findOrFail(Room::class, $roomId, ['building', 'floor', 'roomType']);
    }

    // Data Loading
    private function loadFloors(): void
    {
        $this->floors = $this->selectedBuilding
            ? Floor::where('building_id', $this->selectedBuilding)->orderBy('nomor_lantai')->get()
            : Floor::orderBy('nomor_lantai')->get();
    }

    private function loadFloorsForRoom($buildingId): void
    {
        $this->room_floor_id = '';
        $this->floorsForRoom = $buildingId ? Floor::where('building_id', $buildingId)->get() : [];
    }

    // Query Methods
    public function getStatsProperty()
    {
        return $this->getBaseRoomQuery()
            ->selectRaw("COUNT(CASE WHEN status='kosong' THEN 1 END) as kosong")
            ->selectRaw("COUNT(CASE WHEN status='terisi' THEN 1 END) as terisi")
            ->selectRaw("COUNT(CASE WHEN status='booking' THEN 1 END) as booking")
            ->first()
            ->toArray();
    }

    private function getBaseRoomQuery()
    {
        return Room::query()
            ->when($this->selectedBuilding, fn($q) => $q->where('building_id', $this->selectedBuilding))
            ->when($this->selectedFloor, fn($q) => $q->where('floor_id', $this->selectedFloor));
    }

    private function getBuildings()
    {
        return Building::withCount(['rooms', 'floors'])->get();
    }

    private function getAllFloors()
    {
        return Floor::with('building')->withCount('rooms')->orderBy('building_id')->orderBy('nomor_lantai')->get();
    }

    private function getRoomTypes()
    {
        return RoomType::withCount('rooms')->get();
    }

    private function getRooms()
    {
        return Room::with(['building', 'floor', 'roomType'])
            ->orderBy('building_id')
            ->orderBy('floor_id')
            ->orderBy('nomor_kamar')
            ->get();
    }

    private function getRoomsByFloor()
    {
        if (!$this->selectedBuilding) {
            return [];
        }

        return Room::whereHas('floor', fn($q) => $q->where('building_id', $this->selectedBuilding))
            ->with(['floor', 'roomType'])
            ->get()
            ->groupBy('floor.nomor_lantai');
    }

    // Validation Rules - PERBAIKAN UTAMA
    protected function rules(): array
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
            'status' => 'required|in:' . implode(',', [self::STATUS_KOSONG, self::STATUS_TERISI, self::STATUS_BOOKING]),
        ];
    }

    // Helpers
    private function findOrFail($model, $id, array $relations = [])
    {
        $query = $model::query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        return $query->findOrFail($id);
    }

    private function handleSaveSuccess(string $entity): void
    {
        $isEditing = $this->editingRoomId || $this->editingBuildingId || $this->editingFloorId || $this->editingRoomTypeId;
        $action = $isEditing ? 'diupdate' : 'ditambahkan';

        $this->flashSuccess("$entity berhasil $action!");
    }

    private function flashSuccess(string $message): void
    {
        session()->flash('message', $message);
    }

    private function flashError(string $message): void
    {
        session()->flash('error', $message);
    }

    // Render
    public function render()
    {
        return view('livewire.kamar.index', [
            'buildings' => $this->getBuildings(),
            'allFloors' => $this->getAllFloors(),
            'roomTypes' => $this->getRoomTypes(),
            'rooms' => $this->getRooms(),
            'roomsByFloor' => $this->getRoomsByFloor(),
            'stats' => $this->getStatsProperty(),
        ]);
    }
}