<?php

namespace App\Livewire\Laporan;

use Livewire\Component;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Livewire\Laporan\LaporanKeuanganExport;

class LaporanKeuangan extends Component
{
    public $startDate;
    public $endDate;
    public $status = 'lunas'; // default hanya transaksi lunas

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }

    public function getTransaksiProperty()
    {
        return Pembayaran::with(['penghuni', 'room'])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTotalProperty()
    {
        return $this->transaksi->sum('jumlah');
    }

    public function exportExcel()
    {
        return Excel::download(new LaporanKeuanganExport(
            $this->startDate,
            $this->endDate,
            $this->status
        ), 'laporan_keuangan.xlsx');
    }

    public function exportPdf()
    {
        $data = [
            'transaksi' => $this->transaksi,
            'total' => $this->total,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $pdf = Pdf::loadView('livewire.laporan.laporan-keuangan-export', $data);
        return response()->streamDownload(fn() => print ($pdf->output()), 'laporan_keuangan.pdf');
    }

    public function render()
    {
        return view('livewire.laporan.laporan-keuangan', [
            'transaksi' => $this->transaksi,   // <-- penting
            'total' => $this->total,       // <-- penting
        ]);
    }
}
