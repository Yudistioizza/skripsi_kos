<?php

namespace App\Livewire\Penghuni;

use Livewire\Component;
use App\Models\Penghuni;
use App\Models\PenghuniVerifikasi;
use App\Models\Room;
use App\Models\Floor;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class PenghuniRegister extends Component
{
    use WithFileUploads;

    /* ---------- Form Fields ---------- */
    public $nama, $email, $no_hp, $alamat;
    public $ktp_file, $perjanjian_file;
    public $room_id, $tanggal_masuk, $catatan;

    /* ---------- UI State ---------- */
    public $submitted = false;
    public $selectedBuilding = '';
    public $selectedFloor = '';
    public $floors = [];
    public $rooms = [];

    /* ---------- Lifecycle ---------- */
    public function mount()
    {
        $this->resetFormState();
    }

    public function render()
    {
        return view('livewire.penghuni.penghuni-register', [
            'rooms' => Room::where('status', 'tersedia')->get(),
        ])->layout('guest');
    }

    /* ---------- Validation ---------- */
    protected function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:penghuni,email',
            'no_hp' => 'required|string|max:20',
            'alamat' => 'required|string',
            'ktp_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'perjanjian_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'room_id' => 'nullable|exists:rooms,id',
            'tanggal_masuk' => 'required|date|after_or_equal:today',
            'catatan' => 'nullable|string|max:1000',
        ];
    }

    protected function messages()
    {
        return [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'ktp_file.required' => 'Upload KTP wajib dilakukan',
            'ktp_file.max' => 'Ukuran file KTP maksimal 2MB',
            'tanggal_masuk.after_or_equal' => 'Tanggal masuk tidak boleh kurang dari hari ini',
        ];
    }

    /* ---------- Dropdown Cascade ---------- */
    public function updatedSelectedBuilding($id)
    {
        $this->resetFloorAndRoom();
        $this->floors = $id ? $this->getFloorsByBuilding($id) : [];
    }

    public function updatedSelectedFloor($id)
    {
        $this->room_id = '';
        $this->rooms = $id ? $this->getAvailableRooms($id) : [];
    }

    private function getFloorsByBuilding($buildingId)
    {
        return Floor::where('building_id', $buildingId)
            ->orderBy('nomor_lantai')
            ->get();
    }

    private function getAvailableRooms($floorId)
    {
        return Room::where('floor_id', $floorId)
            ->whereIn('status', ['kosong', 'booking'])
            ->with('roomType')
            ->orderBy('nomor_kamar')
            ->get();
    }

    private function resetFloorAndRoom()
    {
        $this->selectedFloor = '';
        $this->room_id = '';
        $this->rooms = [];
    }

    /* ---------- Submit ---------- */
    public function submit()
    {
        $this->validate();

        try {
            $penghuni = $this->createPenghuni();
            $this->createInitialVerification($penghuni);
            $this->bookSelectedRoom();

            $this->markAsSubmitted();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function createPenghuni()
    {
        return Penghuni::create([
            'nama' => $this->nama,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
            'ktp' => $this->uploadFile('ktp_file', 'penghuni/ktp'),
            'perjanjian' => $this->uploadFile('perjanjian_file', 'penghuni/perjanjian'),
            'room_id' => $this->room_id,
            'tanggal_masuk' => $this->tanggal_masuk,
            'status' => 'menunggu_verifikasi',
            'catatan' => $this->catatan,
        ]);
    }

    private function createInitialVerification($penghuni)
    {
        PenghuniVerifikasi::create([
            'penghuni_id' => $penghuni->id,
            'verified_by' => null,
            'status' => 'menunggu_verifikasi',
            'catatan' => 'Pendaftaran awal melalui form publik',
            'verified_at' => now(),
        ]);
    }

    private function bookSelectedRoom()
    {
        if ($this->room_id) {
            Room::where('id', $this->room_id)
                ->update(['status' => 'booking']);
        }
    }

    private function uploadFile($field, $path)
    {
        return $this->{$field} ? $this->{$field}->store($path, 'public') : null;
    }

    private function markAsSubmitted()
    {
        $this->submitted = true;
        $this->resetForm();
    }

    /* ---------- Reset ---------- */
    public function resetForm()
    {
        $this->resetFormState();
        $this->resetValidation();
    }

    private function resetFormState()
    {
        $this->reset([
            'nama',
            'email',
            'no_hp',
            'alamat',
            'ktp_file',
            'perjanjian_file',
            'room_id',
            'tanggal_masuk',
            'catatan',
            'selectedBuilding',
            'selectedFloor'
        ]);

        $this->floors = [];
        $this->rooms = [];
        $this->submitted = false;
    }
}