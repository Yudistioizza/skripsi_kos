<?php

namespace App\Livewire\Penghuni;

use Livewire\Component;
use App\Models\Penghuni;
use App\Models\PenghuniVerifikasi;
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

    /* ---------- Validation ---------- */
    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                $this->penghuniId
                ? Rule::unique('penghuni', 'email')->ignore($this->penghuniId)
                : Rule::unique('penghuni', 'email'),
            ],
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'ktp_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'perjanjian_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:menunggu_verifikasi,aktif,ditolak,keluar',
            'room_id' => [
                'nullable',
                Rule::exists('rooms', 'id')->where(function ($q) {
                    $q->whereIn('status', ['kosong', 'booking']);
                }),
            ],
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'catatan' => 'nullable|string',
        ];
    }

    /* ---------- Render ---------- */
    public function render()
    {
        $query = Penghuni::with(['kamar.floor.building', 'verifier'])
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('no_hp', 'like', '%' . $this->search . '%');
            }))
            ->latest();

        return view('livewire.penghuni.penghuni-manager', [
            'penghunis' => $query->paginate(10),
            'statusCounts' => $this->getStatusCounts(),
        ]);
    }

    /* ---------- Status Counts ---------- */
    private function getStatusCounts()
    {
        return collect(['menunggu_verifikasi', 'aktif', 'ditolak', 'keluar'])
            ->mapWithKeys(fn($s) => [$s => Penghuni::where('status', $s)->count()]);
    }

    /* ---------- Modal Controls ---------- */
    public function openModal()
    {
        $this->resetForm();
        $this->selectedBuildingForm = '';
        $this->selectedFloorForm = '';
        $this->floorsForForm = [];
        $this->roomsForForm = [];
        $this->showModal = true;
        $this->isEdit = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
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
        $this->roomsForForm = $id
            ? Room::where('floor_id', $id)
                ->whereIn('status', ['kosong', 'booking']) // << tambahan
                ->orderBy('nomor_kamar')
                ->get()
            : [];
    }

    /* ---------- Edit ---------- */
    public function edit($id)
    {
        $penghuni = Penghuni::with('kamar.floor')->findOrFail($id);
        $this->fill($penghuni->only([
            'nama',
            'email',
            'no_hp',
            'alamat',
            'status',
            'room_id',
            'tanggal_masuk',
            'tanggal_keluar',
            'catatan'
        ]));
        $this->penghuniId = $penghuni->id;
        $this->tanggal_masuk = optional($penghuni->tanggal_masuk)->format('Y-m-d');
        $this->tanggal_keluar = optional($penghuni->tanggal_keluar)->format('Y-m-d');

        if ($penghuni->room) {
            $this->selectedBuildingForm = $penghuni->room->floor->building_id;
            $this->updatedSelectedBuildingForm($this->selectedBuildingForm);
            $this->selectedFloorForm = $penghuni->room->floor_id;
            $this->updatedSelectedFloorForm($this->selectedFloorForm);
        }

        $this->showModal = true;
        $this->isEdit = true;
    }

    /* ---------- Save ---------- */
    public function save()
    {
        $this->validate();
        try {
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
            if ($this->room_id) {
                // Jika penghuni ini statusnya aktif → kamar jadi terisi
                if ($this->status === 'aktif') {
                    Room::where('id', $this->room_id)->update(['status' => 'terisi']);
                } else {
                    // kalau masih menunggu/ditolak/keluar → biarkan kosong/booking
                    // (tidak mengubah status kamar)
                }
            }

            if ($this->isEdit) {
                $penghuni = Penghuni::find($this->penghuniId);
                $oldRoom = $penghuni->room_id;   // simpan sebelum update

                /* kalau ganti kamar */
                if ($oldRoom && $oldRoom != $this->room_id) {
                    // kamar lama → kosong
                    Room::where('id', $oldRoom)->update(['status' => 'kosong']);
                }

                /* mark kamar baru (hanya bila penghuni sekarang aktif) */
                if ($this->room_id && $this->status === 'aktif') {
                    Room::where('id', $this->room_id)->update(['status' => 'terisi']);
                }
                if ($this->ktp_file && $penghuni->ktp)
                    Storage::disk('public')->delete($penghuni->ktp);
                if ($this->perjanjian_file && $penghuni->perjanjian)
                    Storage::disk('public')->delete($penghuni->perjanjian);
                $penghuni->update($data);
                session()->flash('message', 'Data penghuni berhasil diupdate.');
            } else {
                Penghuni::create($data);
                session()->flash('message', 'Data penghuni berhasil ditambahkan.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    /* ---------- Delete ---------- */
    public function delete($id)
    {
        try {
            $penghuni = Penghuni::findOrFail($id);

            /* kembalikan kamar ke kosong */
            if ($penghuni->room_id) {
                Room::where('id', $penghuni->room_id)->update(['status' => 'kosong']);
            }

            // hapus file
            if ($penghuni->ktp)
                Storage::disk('public')->delete($penghuni->ktp);
            if ($penghuni->perjanjian)
                Storage::disk('public')->delete($penghuni->perjanjian);

            $penghuni->delete();
            session()->flash('message', 'Data penghuni berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
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
            'status',
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

        $this->verifyPenghuniId = $id;
        $this->verifyStatus = '';
        $this->verifyCatatan = '';
        $this->roomIsOccupied = false;

        // kalau kamar sudah TERISI oleh penghuni LAIN
        if ($penghuni->kamar && $penghuni->kamar->status === 'terisi') {
            // pastikan yang mengisi BUKAN dirinya sendiri
            $occupiedByOther = Penghuni::where('room_id', $penghuni->room_id)
                ->where('status', 'aktif')
                ->where('id', '!=', $penghuni->id)
                ->exists();

            $this->roomIsOccupied = $occupiedByOther;
        }

        $this->showVerifyModal = true;
    }

    public function closeVerifyModal()
    {
        $this->showVerifyModal = false;
        $this->verifyPenghuniId = null;
        $this->verifyStatus = '';
        $this->verifyCatatan = '';
    }

    public function verify()
    {
        $this->validate([
            'verifyStatus' => 'required|in:aktif,ditolak',
            'verifyCatatan' => 'nullable|string',
        ]);

        // blok jika mau menerima tapi kamar sudah terisi orang lain
        if ($this->verifyStatus === 'aktif' && $this->roomIsOccupied) {
            session()->flash('error', 'Kamar ini sudah ditempati penghuni aktif lain. Silakan tolak atau minta penghuni mengganti kamar.');
            return;
        }

        // lanjut proses seperti biasa
        $penghuni = Penghuni::findOrFail($this->verifyPenghuniId);
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

        session()->flash('message', 'Verifikasi berhasil disimpan.');
        $this->closeVerifyModal();
    }
}