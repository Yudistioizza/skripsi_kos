<?php

namespace App\Livewire\Laporan;

use App\Models\Pembayaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanKeuanganExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate, $endDate, $status;

    public function __construct($startDate, $endDate, $status)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function collection()
    {
        return Pembayaran::with(['penghuni', 'room'])
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Transaksi',
            'Penghuni',
            'Kamar',
            'Jumlah',
            'Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d/m/Y'),
            $row->kode_transaksi,
            $row->penghuni->nama ?? '-',
            $row->room->nama ?? '-',
            number_format($row->jumlah, 2, ',', '.'),
            ucfirst($row->status),
        ];
    }

    public function render()
    {
        return view('livewire.laporan.laporan-keuangan-export');
    }
}
