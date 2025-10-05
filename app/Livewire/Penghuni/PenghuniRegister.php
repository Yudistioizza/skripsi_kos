<?php

namespace App\Livewire\Penghuni;

use Livewire\Component;
use App\Models\Penghuni;
use App\Models\PenghuniVerifikasi;
use App\Models\Room;
use Livewire\WithFileUploads;

class PenghuniRegister extends Component
{
    use WithFileUploads;

    public $nama;
    public $email;
    public $no_hp;
    public $alamat;
    public $ktp_file;
    public $perjanjian_file;
    public $room_id;
    public $tanggal_masuk;
    public $catatan;

    public $submitted = false;

    protected $rules = [
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

    protected $messages = [
        'nama.required' => 'Nama wajib diisi',
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'email.unique' => 'Email sudah terdaftar',
        'no_hp.required' => 'Nomor HP wajib diisi',
        'alamat.required' => 'Alamat wajib diisi',
        'ktp_file.required' => 'Upload KTP wajib dilakukan',
        'ktp_file.mimes' => 'Format file KTP harus jpg, jpeg, png, atau pdf',
        'ktp_file.max' => 'Ukuran file KTP maksimal 2MB',
        'perjanjian_file.mimes' => 'Format file perjanjian harus jpg, jpeg, png, atau pdf',
        'perjanjian_file.max' => 'Ukuran file perjanjian maksimal 2MB',
        'tanggal_masuk.required' => 'Tanggal masuk wajib diisi',
        'tanggal_masuk.after_or_equal' => 'Tanggal masuk tidak boleh kurang dari hari ini',
    ];

    public function render()
    {
        return view('livewire.penghuni.penghuni-register', [
            'rooms' => Room::where('status', 'tersedia')->get(),
        ])->layout('guest');
    }

    public function submit()
    {
        $this->validate();

        try {
            // Upload KTP
            $ktpPath = $this->ktp_file->store('penghuni/ktp', 'public');

            // Upload Perjanjian (jika ada)
            $perjanjianPath = null;
            if ($this->perjanjian_file) {
                $perjanjianPath = $this->perjanjian_file->store('penghuni/perjanjian', 'public');
            }

            // Simpan data penghuni
            $penghuni = Penghuni::create([
                'nama' => $this->nama,
                'email' => $this->email,
                'no_hp' => $this->no_hp,
                'alamat' => $this->alamat,
                'ktp' => $ktpPath,
                'perjanjian' => $perjanjianPath,
                'room_id' => $this->room_id,
                'tanggal_masuk' => $this->tanggal_masuk,
                'status' => 'menunggu_verifikasi',
                'catatan' => $this->catatan,
            ]);

            // Simpan riwayat verifikasi awal
            PenghuniVerifikasi::create([
                'penghuni_id' => $penghuni->id,
                'verified_by' => null,
                'status' => 'menunggu_verifikasi',
                'catatan' => 'Pendaftaran awal melalui form publik',
                'verified_at' => now(),
            ]);

            $this->submitted = true;
            $this->reset(['nama', 'email', 'no_hp', 'alamat', 'ktp_file', 'perjanjian_file', 'room_id', 'tanggal_masuk', 'catatan']);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->submitted = false;
        $this->reset();
        $this->resetValidation();
    }
}
