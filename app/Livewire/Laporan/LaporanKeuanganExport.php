<?php

namespace App\Livewire\Laporan;

use App\Models\Pembayaran;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;   // composer require maatwebsite/excel
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanKeuanganExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly string $startDate,
        private readonly string $endDate,
        private readonly string $status
    ) {
    }

    public function collection(): Collection
    {
        return $this->query()->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Kode Transaksi', 'Penghuni', 'Kamar', 'Jumlah', 'Status'];
    }

    public function map($row): array
    {
        return [
            $row->created_at->format('d/m/Y'),
            $row->kode_transaksi,
            $row->penghuni->nama ?? '-',
            $row->room->nomor_kamar ?? '-',
            number_format((float) $row->jumlah, 2, ',', '.'),
            ucfirst($row->status),
        ];
    }

    /* ----------  helpers  ---------- */

    private function query()
    {
        return Pembayaran::with(['penghuni', 'room'])
            ->where('status', $this->status)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->latest();
    }
}