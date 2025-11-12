<?php

namespace App\Livewire\Penghuni;

use Livewire\Component;
use App\Models\Penghuni;
use App\Models\Room;
use App\Models\Floor;
use App\Models\Building;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PenghuniManager extends Component
{
    use WithFileUploads, WithPagination;

    /* ---------- Form Fields ---------- */
    public $penghuniId, $nama, $email, $no_hp, $alamat;
    public $ktp_file, $perjanjian_file, $status = 'menunggu_verifikasi';
    public $room_id, $tanggal_masuk, $tanggal_keluar, $catatan;

    /* ---------- UI State ---------- */
    public $showModal = false, $showVerifyModal = false, $isEdit = false;
    public $filterStatus = '', $search = '';

    /* ---------- Verifikasi ---------- */
    public $verifyPenghuniId, $verifyStatus, $verifyCatatan;

    /* ---------- Dropdown Cascade ---------- */
    public $selectedBuildingForm = '';
    public $selectedFloorForm = '';
    public $floorsForForm = [];
    public $roomsForForm = [];
    public $roomIsOccupied = false;

    protected $paginationTheme = 'tailwind';

    /* ---------- Delete Confirmation ---------- */
    public $showDeleteModal = false;
    public $deleteId = null;
    public $deleteNama = '';

    /* ---------- Validation ---------- */
    protected function rules()
    {
        $emailRule = Rule::unique('penghuni', 'email');
        if ($this->penghuniId) {
            $emailRule->ignore($this->penghuniId);
        }

        return [
            'nama' => 'required|string|max:255',
            'email' => ['nullable', 'email', $emailRule],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'ktp_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'perjanjian_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:menunggu_verifikasi,aktif,ditolak,keluar',
            'room_id' => [
                'nullable',
                Rule::exists('rooms', 'id')->where(fn($q) => $q->whereIn('status', ['kosong', 'booking'])),
            ],
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'catatan' => 'nullable|string',
        ];
    }

    /* ---------- Render ---------- */
    public function render()
    {
        return view('livewire.penghuni.penghuni-manager', [
            'penghunis' => $this->getPenghunis(),
            'statusCounts' => $this->getStatusCounts(),
        ]);
    }

    private function getPenghunis()
    {
        return Penghuni::with(['kamar.floor.building', 'verifier'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn($q) => $q->where(fn($q) => $q
                ->where('nama', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('no_hp', 'like', "%{$this->search}%")))
            ->latest()
            ->paginate(10);
    }

    private function getStatusCounts()
    {
        return collect(['menunggu_verifikasi', 'aktif', 'ditolak', 'keluar'])
            ->mapWithKeys(fn($status) => [$status => Penghuni::where('status', $status)->count()]);
    }

    /* ---------- Modal Controls ---------- */
    public function openModal()
    {
        $this->resetForm();
        $this->resetCascadeDropdown();
        $this->showModal = true;
        $this->isEdit = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function openDeleteModal($id)
    {
        $penghuni = Penghuni::findOrFail($id);
        $this->deleteId = $id;
        $this->deleteNama = $penghuni->nama;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->reset(['deleteId', 'deleteNama']);
    }

    public function confirmDelete()
    {
        $this->delete($this->deleteId);
        $this->closeDeleteModal();
    }

    /* ---------- Cascade Dropdown ---------- */
    public function updatedSelectedBuildingForm($id)
    {
        $this->selectedFloorForm = '';
        $this->room_id = '';
        $this->floorsForForm = $id ? Floor::where('building_id', $id)->orderBy('nomor_lantai')->get() : [];
        $this->roomsForForm = [];
    }

    public function updatedSelectedFloorForm($id)
    {
        $this->room_id = '';
        $this->roomsForForm = $id ? Room::where('floor_id', $id)
            ->whereIn('status', ['kosong', 'booking'])
            ->orderBy('nomor_kamar')
            ->get() : [];
    }

    private function resetCascadeDropdown()
    {
        $this->selectedBuildingForm = '';
        $this->selectedFloorForm = '';
        $this->floorsForForm = [];
        $this->roomsForForm = [];
    }

    /* ---------- Edit ---------- */
    public function edit($id)
    {
        $penghuni = Penghuni::with('kamar.floor')->findOrFail($id);
        $this->fillForm($penghuni);
        $this->loadCascadeDropdown($penghuni);
        $this->showModal = true;
        $this->isEdit = true;
    }

    private function fillForm($penghuni)
    {
        $this->fill($penghuni->only([
            'nama',
            'email',
            'no_hp',
            'alamat',
            'status',
            'room_id',
            'catatan'
        ]));
        $this->penghuniId = $penghuni->id;
        $this->tanggal_masuk = optional($penghuni->tanggal_masuk)->format('Y-m-d');
        $this->tanggal_keluar = optional($penghuni->tanggal_keluar)->format('Y-m-d');
    }

    private function loadCascadeDropdown($penghuni)
    {
        if ($penghuni->room) {
            $this->selectedBuildingForm = $penghuni->room->floor->building_id;
            $this->updatedSelectedBuildingForm($this->selectedBuildingForm);
            $this->selectedFloorForm = $penghuni->room->floor_id;
            $this->updatedSelectedFloorForm($this->selectedFloorForm);
        }
    }

    /* ---------- Save ---------- */
    public function save()
    {
        $this->validate();

        try {
            $data = $this->prepareDataForSave();
            $penghuni = $this->persistData($data);
            $this->handleRoomStatusChanges($penghuni);
            session()->flash('message', $this->isEdit ? 'Data penghuni berhasil diupdate.' : 'Data penghuni berhasil ditambahkan.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    private function prepareDataForSave()
    {
        $data = [
            'nama' => $this->nama,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
            'status' => $this->status,
            'room_id' => $this->room_id,
            'tanggal_masuk' => $this->tanggal_masuk,
            'tanggal_keluar' => $this->tanggal_keluar,
            'catatan' => $this->catatan,
        ];

        if ($this->ktp_file) {
            $data['ktp'] = $this->ktp_file->store('penghuni/ktp', 'public');
        }
        if ($this->perjanjian_file) {
            $data['perjanjian'] = $this->perjanjian_file->store('penghuni/perjanjian', 'public');
        }

        return $data;
    }

    private function persistData($data)
    {
        if ($this->isEdit) {
            $penghuni = Penghuni::findOrFail($this->penghuniId);
            if ($this->hasFileChanges()) {
                $this->deleteOldFiles($penghuni);
            }
            $penghuni->update($data);
            return $penghuni;
        }

        return Penghuni::create($data);
    }

    private function hasFileChanges()
    {
        return $this->ktp_file || $this->perjanjian_file;
    }

    private function handleRoomStatusChanges($penghuni)
    {
        if (!$this->room_id)
            return;

        if ($this->isEdit) {
            $this->updateRoomOnEdit($penghuni);
        } else {
            $this->updateRoomOnCreate();
        }
    }

    private function updateRoomOnEdit($penghuni)
    {
        $oldRoom = $penghuni->getOriginal('room_id');
        if ($oldRoom && $oldRoom != $this->room_id) {
            Room::where('id', $oldRoom)->update(['status' => 'kosong']);
        }
        if ($this->status === 'aktif') {
            Room::where('id', $this->room_id)->update(['status' => 'terisi']);
        }
    }

    private function updateRoomOnCreate()
    {
        if ($this->status === 'aktif') {
            Room::where('id', $this->room_id)->update(['status' => 'terisi']);
        }
    }

    private function deleteOldFiles($penghuni)
    {
        if ($this->ktp_file && $penghuni->ktp) {
            Storage::disk('public')->delete($penghuni->ktp);
        }
        if ($this->perjanjian_file && $penghuni->perjanjian) {
            Storage::disk('public')->delete($penghuni->perjanjian);
        }
    }

    /* ---------- Delete ---------- */
    public function delete($id)
    {
        try {
            $penghuni = Penghuni::findOrFail($id);
            $this->releaseRoom($penghuni);
            $this->deleteAssociatedFiles($penghuni);
            $penghuni->delete();
            session()->flash('message', 'Data penghuni berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    private function releaseRoom($penghuni)
    {
        if ($penghuni->room_id) {
            Room::where('id', $penghuni->room_id)->update(['status' => 'kosong']);
        }
    }

    private function deleteAssociatedFiles($penghuni)
    {
        if ($penghuni->ktp) {
            Storage::disk('public')->delete($penghuni->ktp);
        }
        if ($penghuni->perjanjian) {
            Storage::disk('public')->delete($penghuni->perjanjian);
        }
    }

    /* ---------- Reset Form ---------- */
    public function resetForm()
    {
        $this->reset([
            'penghuniId',
            'nama',
            'email',
            'no_hp',
            'alamat',
            'ktp_file',
            'perjanjian_file',
            'room_id',
            'tanggal_masuk',
            'tanggal_keluar',
            'catatan'
        ]);
        $this->status = 'menunggu_verifikasi';
    }

    /* ---------- Search & Filter ---------- */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    /* ---------- Modal Verifikasi ---------- */
    public function openVerifyModal($id)
    {
        $penghuni = Penghuni::with('kamar')->findOrFail($id);
        $this->setupVerification($penghuni);
        $this->showVerifyModal = true;
    }

    private function setupVerification($penghuni)
    {
        $this->verifyPenghuniId = $penghuni->id;
        $this->verifyStatus = '';
        $this->verifyCatatan = '';
        $this->roomIsOccupied = $this->checkRoomOccupancy($penghuni);
    }

    private function checkRoomOccupancy($penghuni)
    {
        if (!$penghuni->kamar || $penghuni->kamar->status !== 'terisi') {
            return false;
        }

        return Penghuni::where('room_id', $penghuni->room_id)
            ->where('status', 'aktif')
            ->where('id', '!=', $penghuni->id)
            ->exists();
    }

    public function closeVerifyModal()
    {
        $this->showVerifyModal = false;
        $this->reset(['verifyPenghuniId', 'verifyStatus', 'verifyCatatan']);
    }

    public function verify()
    {
        $this->validate([
            'verifyStatus' => 'required|in:aktif,ditolak',
            'verifyCatatan' => 'nullable|string',
        ]);

        if ($this->shouldBlockVerification()) {
            return $this->blockVerification();
        }

        $penghuni = Penghuni::findOrFail($this->verifyPenghuniId);
        $this->applyVerification($penghuni);

        session()->flash('message', 'Verifikasi berhasil disimpan.');
        $this->closeVerifyModal();
    }

    private function shouldBlockVerification()
    {
        return $this->verifyStatus === 'aktif' && $this->roomIsOccupied;
    }

    private function blockVerification()
    {
        session()->flash('error', 'Kamar ini sudah ditempati penghuni aktif lain. Silakan tolak atau minta penghuni mengganti kamar.');
    }

    private function applyVerification($penghuni)
    {
        $penghuni->update([
            'status' => $this->verifyStatus,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan' => $this->verifyCatatan,
        ]);

        if ($this->verifyStatus === 'aktif' && $penghuni->room_id) {
            Room::where('id', $penghuni->room_id)->update(['status' => 'terisi']);
        }

        PenghuniVerifikasi::create([
            'penghuni_id' => $this->verifyPenghuniId,
            'verified_by' => auth()->id(),
            'status' => $this->verifyStatus,
            'catatan' => $this->verifyCatatan,
            'verified_at' => now(),
        ]);
    }
}