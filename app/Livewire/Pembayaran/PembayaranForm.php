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

    public $penghuni_id;
    public $nama_penghuni;
    public $email;
    public $no_hp;
    public $jumlah;
    public $periode_mulai;
    public $periode_selesai;
    public $metode = 'transfer';
    public $catatan;
    public $bukti_file;

    public $submitted = false;
    public $kode_transaksi;

    protected function rules()
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

    protected $messages = [
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

    public function mount()
    {
        $this->periode_mulai = now()->startOfMonth()->format('Y-m-d');
        $this->periode_selesai = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.pembayaran.pembayaran-form')
            ->layout('guest');
    }

    public function submit()
    {
        $this->validate();

        try {
            // Cari atau buat penghuni berdasarkan email
            $penghuni = Penghuni::firstOrCreate(
                ['email' => $this->email],
                [
                    'nama' => $this->nama_penghuni,
                    'no_hp' => $this->no_hp,
                ]
            );

            // Buat pembayaran
            $pembayaran = Pembayaran::create([
                'penghuni_id' => $penghuni->id,
                'jumlah' => $this->jumlah,
                'periode_mulai' => $this->periode_mulai,
                'periode_selesai' => $this->periode_selesai,
                'status' => 'menunggu_verifikasi',
                'metode' => $this->metode,
                'catatan' => $this->catatan,
            ]);

            // Upload bukti pembayaran
            if ($this->bukti_file) {
                $path = $this->bukti_file->store('pembayaran/bukti', 'public');
                $extension = $this->bukti_file->getClientOriginalExtension();

                PembayaranBukti::create([
                    'pembayaran_id' => $pembayaran->id,
                    'file_path' => $path,
                    'tipe' => $extension,
                ]);
            }

            $this->kode_transaksi = $pembayaran->kode_transaksi;
            $this->submitted = true;

            // Reset form
            $this->reset([
                'nama_penghuni',
                'email',
                'no_hp',
                'jumlah',
                'catatan',
                'bukti_file'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->submitted = false;
        $this->kode_transaksi = null;
        $this->reset();
        $this->mount();
    }
}
