<?php

namespace App\Livewire\Pembayaran;

use Livewire\Component;
use App\Models\Pembayaran;
use App\Models\PembayaranBukti;
use App\Models\Penghuni;
use Livewire\WithFileUploads;

class PembayaranForm extends Component
{
    use WithFileUploads;

    // Constants
    private const MIN_SEARCH_LENGTH = 2;
    private const MAX_SUGGESTIONS = 5;

    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    // Properties
    public $penghuni_id, $nama_penghuni, $email, $no_hp;
    public $jumlah, $periode_mulai, $periode_selesai;
    public $metode = 'transfer', $catatan, $bukti_file;
    public $submitted = false, $kode_transaksi;
    public $suggestions; // Ubah dari array ke Collection
    public $showSuggest = false;

    // Lifecycle
    public function mount(): void
    {
        $this->suggestions = collect(); // Inisialisasi sebagai Collection
        $this->setDefaultPeriode();
    }

    public function render()
    {
        // Hapus ->layout() untuk kompatibilitas Livewire 3
        return view('livewire.pembayaran.pembayaran-form');
    }

    // Event Handlers
    public function updated($field): void
    {
        if ($this->shouldTriggerSearch($field)) {
            $this->searchPenghuni();
        }
    }

    // Public Methods
    public function searchPenghuni(): void
    {
        $this->resetSuggestions();

        if (!$this->hasMinSearchLength()) {
            return;
        }

        $this->suggestions = $this->findPenghuniSuggestions();
        $this->showSuggest = $this->hasSuggestions();

        if ($this->hasExactMatch()) {
            $this->selectPenghuni($this->suggestions->first()->id);
        }
    }

    public function selectPenghuni($id): void
    {
        // Gunakan Collection method firstWhere
        $penghuni = $this->suggestions->firstWhere('id', $id);

        if ($penghuni) {
            $this->fillPenghuniData($penghuni);
        }

        $this->hideSuggestions();
    }

    public function submit(): void
    {
        $this->validate();

        try {
            $penghuni = $this->findOrCreatePenghuni();
            $pembayaran = $this->createPembayaran($penghuni);
            $this->handleFileUpload($pembayaran);
            $this->setSuccessState($pembayaran);
            $this->resetForm();
        } catch (\Exception $e) {
            $this->flashError($e->getMessage());
        }
    }

    public function resetForm(): void
    {
        $this->submitted = false;
        $this->kode_transaksi = null;
        $this->resetExcept(['suggestions', 'showSuggest']);
        $this->setDefaultPeriode();
        $this->hideSuggestions();
    }

    // Private Helper Methods
    private function setDefaultPeriode(): void
    {
        $this->periode_mulai = now()->startOfMonth()->format('Y-m-d');
        $this->periode_selesai = now()->endOfMonth()->format('Y-m-d');
    }

    private function shouldTriggerSearch($field): bool
    {
        return in_array($field, ['nama_penghuni', 'email']);
    }

    private function hasMinSearchLength(): bool
    {
        return strlen($this->nama_penghuni) >= self::MIN_SEARCH_LENGTH ||
            strlen($this->email) >= self::MIN_SEARCH_LENGTH;
    }

    private function resetSuggestions(): void
    {
        $this->suggestions = collect();
        $this->showSuggest = false;
    }

    private function hideSuggestions(): void
    {
        $this->showSuggest = false;
    }

    private function hasSuggestions(): bool
    {
        return $this->suggestions->isNotEmpty();
    }

    private function findPenghuniSuggestions()
    {
        return Penghuni::with('kamar.roomType')
            ->hasActiveRoom()
            ->when($this->nama_penghuni, fn($q) => $q->where('nama', 'like', "%{$this->nama_penghuni}%"))
            ->when($this->email, fn($q) => $q->where('email', 'like', "%{$this->email}%"))
            ->limit(self::MAX_SUGGESTIONS)
            ->get();
    }

    private function hasExactMatch(): bool
    {
        if ($this->suggestions->count() !== 1) {
            return false;
        }

        $suggestion = $this->suggestions->first();

        return strcasecmp($suggestion->nama, $this->nama_penghuni) === 0 &&
            strcasecmp($suggestion->email, $this->email) === 0;
    }

    private function fillPenghuniData(Penghuni $penghuni): void
    {
        $this->nama_penghuni = $penghuni->nama;
        $this->email = $penghuni->email;
        $this->no_hp = $penghuni->no_hp;
        $this->jumlah = $penghuni->kamar?->roomType?->harga ?? 0;

        $this->setPeriodeFromPenghuni($penghuni);
    }

    private function setPeriodeFromPenghuni(Penghuni $penghuni): void
    {
        $this->periode_mulai = optional($penghuni->tanggal_masuk)->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d');
        $this->periode_selesai = optional($penghuni->tanggal_keluar)->format('Y-m-d') ?? now()->endOfMonth()->format('Y-m-d');
    }

    private function findOrCreatePenghuni(): Penghuni
    {
        return Penghuni::firstOrCreate(
            ['email' => $this->email],
            [
                'nama' => $this->nama_penghuni,
                'no_hp' => $this->no_hp,
            ]
        );
    }

    private function createPembayaran(Penghuni $penghuni): Pembayaran
    {
        return Pembayaran::create([
            'penghuni_id' => $penghuni->id,
            'room_id' => $penghuni->room_id,
            'jumlah' => $this->jumlah,
            'periode_mulai' => $this->periode_mulai,
            'periode_selesai' => $this->periode_selesai,
            'status' => self::STATUS_MENUNGGU_VERIFIKASI,
            'metode' => $this->metode,
            'catatan' => $this->catatan,
        ]);
    }

    private function handleFileUpload(Pembayaran $pembayaran): void
    {
        if (!$this->bukti_file) {
            return;
        }

        $path = $this->bukti_file->store('pembayaran/bukti', 'public');

        PembayaranBukti::create([
            'pembayaran_id' => $pembayaran->id,
            'file_path' => $path,
            'tipe' => $this->bukti_file->getClientOriginalExtension(),
        ]);
    }

    private function setSuccessState(Pembayaran $pembayaran): void
    {
        $this->kode_transaksi = $pembayaran->kode_transaksi;
        $this->submitted = true;
    }

    private function flashError(string $message): void
    {
        session()->flash('error', 'Terjadi kesalahan: ' . $message);
    }

    // Validation
    protected function rules(): array
    {
        return [
            'nama_penghuni' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|string|max:20',
            'jumlah' => 'required|numeric|min:0',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'metode' => 'required|in:cash,transfer,e-wallet',
            'catatan' => 'nullable|string|max:1000',
            'bukti_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    protected function messages(): array
    {
        return [
            'nama_penghuni.required' => 'Nama penghuni wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'no_hp.required' => 'Nomor HP wajib diisi',
            'jumlah.required' => 'Jumlah pembayaran wajib diisi',
            'jumlah.numeric' => 'Jumlah harus berupa angka',
            'jumlah.min' => 'Jumlah minimal 0',
            'periode_mulai.required' => 'Periode mulai wajib diisi',
            'periode_selesai.required' => 'Periode selesai wajib diisi',
            'periode_selesai.after_or_equal' => 'Periode selesai harus sama atau setelah periode mulai',
            'metode.required' => 'Metode pembayaran wajib dipilih',
            'bukti_file.required' => 'Bukti pembayaran wajib diunggah',
            'bukti_file.file' => 'File tidak valid',
            'bukti_file.mimes' => 'Format file harus JPG, JPEG, PNG, atau PDF',
            'bukti_file.max' => 'Ukuran file maksimal 2MB',
        ];
    }
}