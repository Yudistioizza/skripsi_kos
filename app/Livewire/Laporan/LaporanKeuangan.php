<?php

namespace App\Livewire\Laporan;

use Livewire\Component;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Livewire\Laporan\LaporanKeuanganExport;

class LaporanKeuangan extends Component
{
    public string $startDate;
    public string $endDate;
    public string $status = 'lunas';

    public function mount(): void
    {
        $this->setDefaultRange();
    }

    /* ----------  API publik (≤ 3 baris)  ---------- */

    public function exportExcel()
    {
        return Excel::download(
            new LaporanKeuanganExport($this->startDate, $this->endDate, $this->status),
            'laporan_keuangan.xlsx'
        );
    }

    public function exportPdf()
    {
        return response()->streamDownload(
            fn() => print Pdf::loadView('livewire.laporan.laporan-keuangan-export', $this->reportData())->output(),
            'laporan_keuangan.pdf'
        );
    }

    public function render()
    {
        return view('livewire.laporan.laporan-keuangan', $this->reportData());
    }

    /* ----------  Query / Helper  ---------- */

    public function getTransaksiProperty()
    {
        return $this->baseQuery()->get();
    }

    public function getTotalProperty()
    {
        return $this->transaksi->sum('jumlah');
    }

    private function baseQuery()
    {
        return Pembayaran::with(['penghuni', 'room'])
            ->where('status', $this->status)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->latest();
    }

    private function reportData(): array
    {
        return [
            'transaksi' => $this->transaksi,
            'total' => $this->total,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }

    private function setDefaultRange(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
    }
}