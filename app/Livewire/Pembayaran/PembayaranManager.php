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
use Carbon\Carbon;

class PembayaranManager extends Component
{
    use WithFileUploads, WithPagination;

    // Constants
    private const DELETE_TYPE_PEMBAYARAN = 'pembayaran';
    private const DELETE_TYPE_BUKTI = 'bukti';

    public const STATUS_LUNAS = 'lunas';
    public const STATUS_DITOLAK = 'ditolak';
    public const STATUS_PENDING = 'pending';

    // Properties
    public $pembayaranId;
    public $penghuni_id, $room_id, $jumlah, $periode_mulai, $periode_selesai;
    public $metode = 'cash', $catatan, $bukti_file;
    public $filterStatus = 'all', $search = '';
    public $showModal = false, $showVerifikasiModal = false, $showBuktiModal = false, $showDeleteModal = false;
    public $verifikasiPembayaranId, $verifikasiStatus = self::STATUS_LUNAS, $verifikasiCatatan;
    public $viewBuktiPembayaran, $deleteItemName = '', $deleteId = null, $deleteType = '';

    protected $paginationTheme = 'tailwind';

    // Rules
    public function rules(): array
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
            'verifikasiStatus' => 'required|in:lunas,ditolak',
            'verifikasiCatatan' => 'nullable|string|max:1000',
        ];
    }

    // Lifecycle
    public function mount(): void
    {
    }

    // Render
    public function render()
    {
        return view('livewire.pembayaran.pembayaran-manager', [
            'pembayaran' => $this->getPembayaranQuery(),
            'penghuni' => Penghuni::hasActiveRoom()->get(),
            'rooms' => Room::all(),
            'menungguVerifikasi' => Pembayaran::menungguVerifikasi()->count(),
            'jatuhTempo' => Pembayaran::jatuhTempo()->count(),
        ]);
    }

    // Query Methods
    private function getPembayaranQuery()
    {
        return Pembayaran::with(['penghuni', 'room', 'verifiedBy', 'bukti'])
            ->filterStatus($this->filterStatus)
            ->search($this->search)
            ->latest()
            ->paginate(10);
    }

    // CRUD Operations
    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id): void
    {
        $this->loadPembayaranForEdit($id);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $pembayaran = $this->persistPembayaran();
            $this->handleBuktiUpload($pembayaran);
            $this->handleSaveSuccess();
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
        }
    }

    public function delete($id): void
    {
        try {
            $this->performDeletePembayaran($id);
            $this->flashSuccess('Pembayaran berhasil dihapus!');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
        }
    }

    // Penghuni Updated
    public function updatedPenghuniId($id): void
    {
        if (!$id) {
            $this->resetForm(); // 1. panggil
            return;             // 2. keluar tanpa nilai
        }

        $penghuni = Penghuni::with('kamar.roomType')->find($id);
        if (!$penghuni) {
            return;
        }

        $this->fillPenghuniData($penghuni);
        $this->setPeriodeFromLastPayment($id, $penghuni);
    }
    // Verifikasi Operations
    public function openVerifikasi($id): void
    {
        $this->verifikasiPembayaranId = $id;
        $this->verifikasiStatus = self::STATUS_LUNAS;
        $this->verifikasiCatatan = '';
        $this->showVerifikasiModal = true;
    }

    public function verifikasi(): void
    {
        $this->validateOnly(['verifikasiStatus', 'verifikasiCatatan']);

        try {
            $this->processVerifikasi();
            $this->closeVerifikasiModal();
            $this->flashSuccess('Pembayaran berhasil diverifikasi!');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
        }
    }

    // Bukti Operations
    public function viewBukti($id): void
    {
        $this->viewBuktiPembayaran = $this->findPembayaranWithBukti($id);
        $this->showBuktiModal = true;
    }

    public function deleteBukti($buktiId): void
    {
        try {
            $this->performDeleteBukti($buktiId);
            $this->refreshViewBukti();
            $this->flashSuccess('Bukti berhasil dihapus!');
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
        }
    }

    // Delete Modal
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
            $this->flashSuccess($this->getDeleteSuccessMessage());
        } catch (\Exception $e) {
            $this->closeDeleteModal();
            $this->flashError($e->getMessage());
        }
    }

    // Close Modals
    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function closeVerifikasiModal(): void
    {
        $this->reset(['verifikasiPembayaranId', 'verifikasiStatus', 'verifikasiCatatan', 'showVerifikasiModal']);
    }

    public function closeBuktiModal(): void
    {
        $this->reset(['viewBuktiPembayaran', 'showBuktiModal']);
    }

    // Pagination Reset
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    // Private Helper Methods
    private function loadPembayaranForEdit($id): void
    {
        $p = Pembayaran::findOrFail($id);

        $this->pembayaranId = $p->id;
        $this->penghuni_id = $p->penghuni_id;
        $this->room_id = $p->room_id;
        $this->jumlah = $p->jumlah;
        $this->periode_mulai = $this->safeFormatDate($p->periode_mulai);
        $this->periode_selesai = $this->safeFormatDate($p->periode_selesai);
        $this->metode = $p->metode;
        $this->catatan = $p->catatan;
    }

    private function persistPembayaran()
    {
        $data = $this->preparePembayaranData();

        if ($this->pembayaranId) {
            return $this->updatePembayaran($data);
        }

        return $this->createPembayaran($data);
    }

    private function preparePembayaranData(): array
    {
        return [
            'penghuni_id' => $this->penghuni_id,
            'room_id' => $this->room_id,
            'jumlah' => $this->jumlah,
            'periode_mulai' => $this->periode_mulai,
            'periode_selesai' => $this->periode_selesai,
            'metode' => $this->metode,
            'catatan' => $this->catatan,
        ];
    }

    private function updatePembayaran(array $data): Pembayaran
    {
        $pembayaran = Pembayaran::findOrFail($this->pembayaranId);
        $pembayaran->update($data);

        $this->flashSuccess('Pembayaran berhasil diperbarui!');

        return $pembayaran;
    }

    private function createPembayaran(array $data): Pembayaran
    {
        $data = array_merge($data, [
            'status' => self::STATUS_LUNAS,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $pembayaran = Pembayaran::create($data);

        $this->createVerifikasiRecord($pembayaran);
        $this->flashSuccess('Pembayaran berhasil ditambahkan!');

        return $pembayaran;
    }

    private function handleBuktiUpload($pembayaran): void
    {
        if (!$this->bukti_file)
            return;

        $path = $this->bukti_file->store('pembayaran/bukti', 'public');

        PembayaranBukti::create([
            'pembayaran_id' => $pembayaran->id,
            'file_path' => $path,
            'tipe' => $this->bukti_file->getClientOriginalExtension(),
            'uploaded_by' => auth()->id(),
        ]);
    }

    private function createVerifikasiRecord(Pembayaran $pembayaran): void
    {
        PembayaranVerifikasi::create([
            'pembayaran_id' => $pembayaran->id,
            'verified_by' => auth()->id(),
            'status' => self::STATUS_LUNAS,
            'catatan' => 'Pembayaran manual oleh admin',
            'verified_at' => now(),
        ]);
    }

    private function fillPenghuniData($penghuni): void
    {
        $this->room_id = $penghuni->kamar?->id;
        $this->jumlah = $penghuni->kamar?->roomType?->harga ?? 0;
    }

    private function setPeriodeFromLastPayment($penghuniId, $penghuni): void
    {
        $lastPayment = Pembayaran::where('penghuni_id', $penghuniId)
            ->latest('periode_selesai')
            ->first();

        if ($lastPayment) {
            $endDate = $this->safeCarbon($lastPayment->periode_selesai);
            $this->periode_mulai = $endDate->copy()->addDay()->format('Y-m-d');
            $this->periode_selesai = $endDate->copy()->addMonth()->format('Y-m-d');
            return;
        }

        $this->periode_mulai = $this->safeFormatDate($penghuni->tanggal_masuk) ?? now()->startOfMonth()->format('Y-m-d');
        $this->periode_selesai = $this->safeFormatDate($penghuni->tanggal_keluar) ?? now()->endOfMonth()->format('Y-m-d');
    }

    private function processVerifikasi(): void
    {
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
    }

    private function findPembayaranWithBukti($id)
    {
        return Pembayaran::with('bukti')->findOrFail($id);
    }

    private function performDeletion(): void
    {
        match ($this->deleteType) {
            self::DELETE_TYPE_PEMBAYARAN => $this->performDeletePembayaran($this->deleteId),
            self::DELETE_TYPE_BUKTI => $this->performDeleteBukti($this->deleteId),
            default => throw new \InvalidArgumentException('Tipe penghapusan tidak valid'),
        };
    }

    private function performDeletePembayaran($id): void
    {
        Pembayaran::findOrFail($id)->delete();
    }

    private function performDeleteBukti($id): void
    {
        PembayaranBukti::findOrFail($id)->delete();
    }

    private function refreshViewBukti(): void
    {
        if ($this->showBuktiModal && $this->viewBuktiPembayaran) {
            $this->viewBuktiPembayaran = $this->findPembayaranWithBukti($this->viewBuktiPembayaran->id);
        }
    }

    private function getDeleteSuccessMessage(): string
    {
        return match ($this->deleteType) {
            self::DELETE_TYPE_PEMBAYARAN => 'Pembayaran berhasil dihapus!',
            self::DELETE_TYPE_BUKTI => 'Bukti berhasil dihapus!',
            default => 'Data berhasil dihapus!',
        };
    }

    private function handleSaveSuccess(): void
    {
        $this->closeModal();
        $this->dispatch('pembayaran-saved');
    }

    private function resetForm(): void
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

    private function flashSuccess(string $message): void
    {
        session()->flash('success', $message);
    }

    private function flashError(string $message): void
    {
        session()->flash('error', 'Terjadi kesalahan: ' . $message);
    }

    // NEW: Safe date handling helpers
    private function safeFormatDate($date): ?string
    {
        if (!$date) {
            return null;
        }
        return $date instanceof \Carbon\CarbonInterface
            ? $date->format('Y-m-d')
            : Carbon::parse($date)->format('Y-m-d');
    }

    private function safeCarbon($date): Carbon
    {
        if ($date instanceof \Carbon\CarbonInterface) {
            return $date;
        }
        return Carbon::parse($date);
    }
}