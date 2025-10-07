<?php

namespace App\Livewire\Penghuni;

use Livewire\Component;
use App\Models\Penghuni;
use App\Models\PenghuniVerifikasi;
use App\Models\Room;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class PenghuniManager extends Component
{
    use WithFileUploads, WithPagination;

    // Properties untuk form
    public $penghuniId;
    public $nama;
    public $email;
    public $no_hp;
    public $alamat;
    public $ktp_file;
    public $perjanjian_file;
    public $status = 'menunggu_verifikasi';
    public $room_id;
    public $tanggal_masuk;
    public $tanggal_keluar;
    public $catatan;

    // Properties untuk modal & filter
    public $showModal = false;
    public $showVerifyModal = false;
    public $isEdit = false;
    public $filterStatus = '';
    public $search = '';

    // Properties untuk verifikasi
    public $verifyPenghuniId;
    public $verifyStatus;
    public $verifyCatatan;

    protected $paginationTheme = 'tailwind';

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
            'ktp_file' => $this->isEdit ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'perjanjian_file' => $this->isEdit ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' : 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:menunggu_verifikasi,aktif,ditolak,keluar',
            'room_id' => 'nullable|exists:rooms,id',
            'tanggal_masuk' => 'nullable|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'catatan' => 'nullable|string',
        ];
    }

    public function render()
    {
        $query = Penghuni::with(['kamar', 'verifier'])
            ->when($this->filterStatus, function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('no_hp', 'like', '%' . $this->search . '%');
                });
            })
            ->latest();

        return view('livewire.penghuni.penghuni-manager', [
            'penghunis' => $query->paginate(10),
            'rooms' => Room::all(),
            'statusCounts' => $this->getStatusCounts(),
        ]);
    }

    private function getStatusCounts()
    {
        return [
            'menunggu_verifikasi' => Penghuni::where('status', 'menunggu_verifikasi')->count(),
            'aktif' => Penghuni::where('status', 'aktif')->count(),
            'ditolak' => Penghuni::where('status', 'ditolak')->count(),
            'keluar' => Penghuni::where('status', 'keluar')->count(),
        ];
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEdit = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function edit($id)
    {
        $penghuni = Penghuni::findOrFail($id);

        $this->penghuniId = $penghuni->id;
        $this->nama = $penghuni->nama;
        $this->email = $penghuni->email;
        $this->no_hp = $penghuni->no_hp;
        $this->alamat = $penghuni->alamat;
        $this->status = $penghuni->status;
        $this->room_id = $penghuni->room_id;
        $this->tanggal_masuk = $penghuni->tanggal_masuk?->format('Y-m-d');
        $this->tanggal_keluar = $penghuni->tanggal_keluar?->format('Y-m-d');
        $this->catatan = $penghuni->catatan;

        $this->showModal = true;
        $this->isEdit = true;
    }

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

            // Upload KTP
            if ($this->ktp_file) {
                $ktpPath = $this->ktp_file->store('penghuni/ktp', 'public');
                $data['ktp'] = $ktpPath;
            }

            // Upload Perjanjian
            if ($this->perjanjian_file) {
                $perjanjianPath = $this->perjanjian_file->store('penghuni/perjanjian', 'public');
                $data['perjanjian'] = $perjanjianPath;
            }

            if ($this->isEdit) {
                $penghuni = Penghuni::findOrFail($this->penghuniId);
                $oldStatus = $penghuni->status;

                // Hapus file lama jika ada upload baru
                if ($this->ktp_file && $penghuni->ktp) {
                    Storage::disk('public')->delete($penghuni->ktp);
                }
                if ($this->perjanjian_file && $penghuni->perjanjian) {
                    Storage::disk('public')->delete($penghuni->perjanjian);
                }

                $penghuni->update($data);

                // Catat perubahan status
                if ($oldStatus !== $this->status) {
                    $this->saveVerificationHistory($penghuni->id, $this->status);
                }

                session()->flash('message', 'Data penghuni berhasil diupdate.');
            } else {
                $penghuni = Penghuni::create($data);

                // Catat riwayat verifikasi awal
                $this->saveVerificationHistory($penghuni->id, $this->status);

                session()->flash('message', 'Data penghuni berhasil ditambahkan.');
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function openVerifyModal($id)
    {
        $this->verifyPenghuniId = $id;
        $this->verifyStatus = '';
        $this->verifyCatatan = '';
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

        try {
            $penghuni = Penghuni::findOrFail($this->verifyPenghuniId);

            $penghuni->update([
                'status' => $this->verifyStatus,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'catatan' => $this->verifyCatatan,
            ]);

            // Simpan riwayat verifikasi
            $this->saveVerificationHistory(
                $this->verifyPenghuniId,
                $this->verifyStatus,
                $this->verifyCatatan
            );

            session()->flash('message', 'Verifikasi penghuni berhasil disimpan.');
            $this->closeVerifyModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function saveVerificationHistory($penghuniId, $status, $catatan = null)
    {
        PenghuniVerifikasi::create([
            'penghuni_id' => $penghuniId,
            'verified_by' => auth()->id(),
            'status' => $status,
            'catatan' => $catatan,
            'verified_at' => now(),
        ]);
    }

    public function delete($id)
    {
        try {
            $penghuni = Penghuni::findOrFail($id);

            // Hapus file
            if ($penghuni->ktp) {
                Storage::disk('public')->delete($penghuni->ktp);
            }
            if ($penghuni->perjanjian) {
                Storage::disk('public')->delete($penghuni->perjanjian);
            }

            $penghuni->delete();

            session()->flash('message', 'Data penghuni berhasil dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->penghuniId = null;
        $this->nama = '';
        $this->email = '';
        $this->no_hp = '';
        $this->alamat = '';
        $this->ktp_file = null;
        $this->perjanjian_file = null;
        $this->status = 'menunggu_verifikasi';
        $this->room_id = null;
        $this->tanggal_masuk = '';
        $this->tanggal_keluar = '';
        $this->catatan = '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
}
