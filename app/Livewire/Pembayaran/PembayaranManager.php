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

    // Form properties
    public $pembayaranId;
    public $penghuni_id;
    public $room_id;
    public $jumlah;
    public $periode_mulai;
    public $periode_selesai;
    public $metode = 'cash';
    public $catatan;
    public $bukti_file;

    // Filter properties
    public $filterStatus = 'all';
    public $search = '';

    // Modal states
    public $showModal = false;
    public $showVerifikasiModal = false;
    public $showBuktiModal = false;

    // Verifikasi properties
    public $verifikasiPembayaranId;
    public $verifikasiStatus;
    public $verifikasiCatatan;

    // View bukti
    public $viewBuktiPembayaran;

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'penghuni_id' => 'required|exists:penghuni,id',
            'room_id' => 'nullable|exists:rooms,id',
            'jumlah' => 'required|numeric|min:0',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'metode' => 'required|in:cash,transfer,e-wallet',
            'catatan' => 'nullable|string|max:1000',
            'bukti_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function mount()
    {
        $this->periode_mulai = now()->startOfMonth()->format('Y-m-d');
        $this->periode_selesai = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $pembayaranQuery = Pembayaran::with(['penghuni', 'room', 'verifiedBy', 'bukti'])
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('kode_transaksi', 'like', '%' . $this->search . '%')
                        ->orWhereHas('penghuni', function ($pq) {
                            $pq->where('nama', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->latest();

        $pembayaran = $pembayaranQuery->paginate(10);

        $penghuni = Penghuni::all();
        $rooms = Room::all();

        // Hitung notifikasi
        $menungguVerifikasi = Pembayaran::menungguVerifikasi()->count();
        $jatuhTempo = Pembayaran::jatuhTempo()->count();

        return view('livewire.pembayaran.pembayaran-manager', [
            'pembayaran' => $pembayaran,
            'penghuni' => $penghuni,
            'rooms' => $rooms,
            'menungguVerifikasi' => $menungguVerifikasi,
            'jatuhTempo' => $jatuhTempo,
        ]);
    }

    public function create()
    {
        $this->reset(['pembayaranId', 'penghuni_id', 'room_id', 'jumlah', 'catatan', 'bukti_file']);
        $this->metode = 'cash';
        $this->periode_mulai = now()->startOfMonth()->format('Y-m-d');
        $this->periode_selesai = now()->endOfMonth()->format('Y-m-d');
        $this->showModal = true;
    }

    public function edit($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $this->pembayaranId = $pembayaran->id;
        $this->penghuni_id = $pembayaran->penghuni_id;
        $this->room_id = $pembayaran->room_id;
        $this->jumlah = $pembayaran->jumlah;
        $this->periode_mulai = $pembayaran->periode_mulai->format('Y-m-d');
        $this->periode_selesai = $pembayaran->periode_selesai->format('Y-m-d');
        $this->metode = $pembayaran->metode ?? 'cash';
        $this->catatan = $pembayaran->catatan;

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
                // Update
                $pembayaran = Pembayaran::findOrFail($this->pembayaranId);
                $pembayaran->update($data);
                $message = 'Pembayaran berhasil diperbarui!';
            } else {
                // Create
                $data['status'] = 'lunas'; // Pembayaran manual langsung lunas
                $data['verified_by'] = auth()->id();
                $data['verified_at'] = now();

                $pembayaran = Pembayaran::create($data);

                // Catat verifikasi
                PembayaranVerifikasi::create([
                    'pembayaran_id' => $pembayaran->id,
                    'verified_by' => auth()->id(),
                    'status' => 'lunas',
                    'catatan' => 'Pembayaran manual oleh admin',
                    'verified_at' => now(),
                ]);

                $message = 'Pembayaran berhasil ditambahkan!';
            }

            // Upload bukti jika ada
            if ($this->bukti_file) {
                $path = $this->bukti_file->store('pembayaran/bukti', 'public');
                $extension = $this->bukti_file->getClientOriginalExtension();

                PembayaranBukti::create([
                    'pembayaran_id' => $pembayaran->id,
                    'file_path' => $path,
                    'tipe' => $extension,
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

    public function delete($id)
    {
        try {
            $pembayaran = Pembayaran::findOrFail($id);
            $pembayaran->delete();

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
            $pembayaran = Pembayaran::findOrFail($this->verifikasiPembayaranId);

            $pembayaran->update([
                'status' => $this->verifikasiStatus,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);

            // Catat riwayat verifikasi
            PembayaranVerifikasi::create([
                'pembayaran_id' => $pembayaran->id,
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

            // Reload data
            $this->viewBuktiPembayaran = Pembayaran::with('bukti')->findOrFail($this->viewBuktiPembayaran->id);

            session()->flash('success', 'Bukti pembayaran berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['pembayaranId', 'penghuni_id', 'room_id', 'jumlah', 'catatan', 'bukti_file']);
    }

    public function closeVerifikasiModal()
    {
        $this->showVerifikasiModal = false;
        $this->reset(['verifikasiPembayaranId', 'verifikasiStatus', 'verifikasiCatatan']);
    }

    public function closeBuktiModal()
    {
        $this->showBuktiModal = false;
        $this->viewBuktiPembayaran = null;
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
