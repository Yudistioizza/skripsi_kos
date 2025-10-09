<?php

namespace App\Livewire\Pembayaran;

use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\PembayaranBukti;
use App\Models\PembayaranVerifikasi;
use App\Models\Penghuni;
use App\Models\Room;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class PembayaranManager extends Component
{
    use WithFileUploads, WithPagination;

    public $pembayaranId;
    public $penghuni_id, $room_id, $jumlah, $periode_mulai, $periode_selesai;
    public $metode = 'cash', $catatan, $bukti_file;
    public $filterStatus = 'all', $search = '';
    public $showModal = false, $showVerifikasiModal = false, $showBuktiModal = false;
    public $verifikasiPembayaranId, $verifikasiStatus = 'lunas', $verifikasiCatatan;
    public $viewBuktiPembayaran;

    protected $paginationTheme = 'tailwind';
    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'tanggal_keluar' => 'datetime',
    ];
    protected function rules()
    {
        return [
            'penghuni_id' => 'required|exists:penghuni,id',
            'room_id' => 'nullable|exists:rooms,id',
            'jumlah' => 'required|numeric|min:0',
            'periode_mulai' => 'required|date|after_or_equal:today',
            'periode_selesai' => 'required|date|after:periode_mulai',
            'metode' => 'required|in:cash,transfer,e-wallet',
            'catatan' => 'nullable|string|max:1000',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function mount()
    {

    }

    public function render()
    {
        $pembayaran = Pembayaran::with(['penghuni', 'room', 'verifiedBy', 'bukti'])
            ->filterStatus($this->filterStatus)
            ->search($this->search)
            ->latest()
            ->paginate(10);

        $penghuni = Penghuni::hasActiveRoom()->get();
        $rooms = Room::all();

        return view('livewire.pembayaran.pembayaran-manager', [
            'pembayaran' => $pembayaran,
            'penghuni' => $penghuni,
            'rooms' => $rooms,
            'menungguVerifikasi' => Pembayaran::menungguVerifikasi()->count(),
            'jatuhTempo' => Pembayaran::jatuhTempo()->count(),
        ]);
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $p = Pembayaran::findOrFail($id);

        $this->pembayaranId = $p->id;
        $this->penghuni_id = $p->penghuni_id;
        $this->room_id = $p->room_id;
        $this->jumlah = $p->jumlah;
        $this->periode_mulai = $p->periode_mulai->format('Y-m-d');
        $this->periode_selesai = $p->periode_selesai->format('Y-m-d');
        $this->metode = $p->metode;
        $this->catatan = $p->catatan;

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'penghuni_id' => $this->penghuni_id,
                'room_id' => $this->room_id,
                'jumlah' => $this->jumlah,
                'periode_mulai' => $this->periode_mulai,
                'periode_selesai' => $this->periode_selesai,
                'metode' => $this->metode,
                'catatan' => $this->catatan,
            ];

            if ($this->pembayaranId) {
                $pembayaran = Pembayaran::findOrFail($this->pembayaranId);
                $pembayaran->update($data);
                $message = 'Pembayaran berhasil diperbarui!';
            } else {
                $data['status'] = 'lunas';
                $data['verified_by'] = auth()->id();
                $data['verified_at'] = now();

                $pembayaran = Pembayaran::create($data);

                PembayaranVerifikasi::create([
                    'pembayaran_id' => $pembayaran->id,
                    'verified_by' => auth()->id(),
                    'status' => 'lunas',
                    'catatan' => 'Pembayaran manual oleh admin',
                    'verified_at' => now(),
                ]);

                $message = 'Pembayaran berhasil ditambahkan!';
            }

            if ($this->bukti_file) {
                $path = $this->bukti_file->store('pembayaran/bukti', 'public');
                PembayaranBukti::create([
                    'pembayaran_id' => $pembayaran->id,
                    'file_path' => $path,
                    'tipe' => $this->bukti_file->getClientOriginalExtension(),
                    'uploaded_by' => auth()->id(),
                ]);
            }

            $this->closeModal();
            session()->flash('success', $message);
            $this->dispatch('pembayaran-saved');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updatedPenghuniId($id)
    {
        if (!$id) {
            return $this->resetForm();
        }

        // Ambil data penghuni beserta kamar dan tipe kamarnya
        $penghuni = Penghuni::with('kamar.roomType')->find($id);

        if (!$penghuni) {
            return;
        }

        // Isi kamar dan harga otomatis
        $this->room_id = $penghuni->kamar?->id;
        $this->jumlah = $penghuni->kamar?->roomType?->harga ?? 0;

        // Ambil pembayaran terakhir penghuni ini
        $lastPayment = \App\Models\Pembayaran::where('penghuni_id', $id)
            ->latest('periode_selesai')
            ->first();

        if ($lastPayment) {
            // Lanjutkan periode dari selesai + 1 hari
            $this->periode_mulai = $lastPayment->periode_selesai->copy()->addDay()->format('Y-m-d');
            $this->periode_selesai = $lastPayment->periode_selesai->copy()->addMonth()->format('Y-m-d');
        } else {
            // Belum pernah bayar → pakai bulan ini
            $this->periode_mulai = optional($penghuni->tanggal_masuk)->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d');
            $this->periode_selesai = optional($penghuni->tanggal_keluar)->format('Y-m-d') ?? now()->endOfMonth()->format('Y-m-d');
        }
    }

    public function delete($id)
    {
        try {
            Pembayaran::findOrFail($id)->delete();
            session()->flash('success', 'Pembayaran berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function openVerifikasi($id)
    {
        $this->verifikasiPembayaranId = $id;
        $this->verifikasiStatus = 'lunas';
        $this->verifikasiCatatan = '';
        $this->showVerifikasiModal = true;
    }

    public function verifikasi()
    {
        $this->validate([
            'verifikasiStatus' => 'required|in:lunas,ditolak',
            'verifikasiCatatan' => 'nullable|string|max:1000',
        ]);

        try {
            $p = Pembayaran::findOrFail($this->verifikasiPembayaranId);
            $p->update([
                'status' => $this->verifikasiStatus,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            PembayaranVerifikasi::create([
                'pembayaran_id' => $p->id,
                'verified_by' => auth()->id(),
                'status' => $this->verifikasiStatus,
                'catatan' => $this->verifikasiCatatan,
                'verified_at' => now(),
            ]);

            $this->closeVerifikasiModal();
            session()->flash('success', 'Pembayaran berhasil diverifikasi!');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function viewBukti($id)
    {
        $this->viewBuktiPembayaran = Pembayaran::with('bukti')->findOrFail($id);
        $this->showBuktiModal = true;
    }

    public function deleteBukti($buktiId)
    {
        try {
            $bukti = PembayaranBukti::findOrFail($buktiId);
            $bukti->delete();
            $this->viewBuktiPembayaran = Pembayaran::with('bukti')->findOrFail($this->viewBuktiPembayaran->id);
            session()->flash('success', 'Bukti berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function closeVerifikasiModal()
    {
        $this->reset(['verifikasiPembayaranId', 'verifikasiStatus', 'verifikasiCatatan', 'showVerifikasiModal']);
    }

    public function closeBuktiModal()
    {
        $this->reset(['viewBuktiPembayaran', 'showBuktiModal']);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->reset([
            'pembayaranId',
            'penghuni_id',
            'room_id',
            'jumlah',
            'catatan',
            'bukti_file',
            'metode'
        ]);
    }

    private function resetPeriode()
    {
        $this->periode_mulai = now()->startOfMonth()->format('Y-m-d');
        $this->periode_selesai = now()->endOfMonth()->format('Y-m-d');
    }
}