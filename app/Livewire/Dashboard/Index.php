<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Penghuni;
use App\Models\Room;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class Index extends Component
{
    private const BULAN_NAMES = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
    private const STATUS_BELUM_LUNAS = 'belum_lunas';

    public function render(): \Illuminate\View\View
    {
        return view('livewire.dashboard.index', $this->prepareViewData());
    }

    private function prepareViewData(): array
    {
        $totalKamar = $this->getTotalKamar();
        $kamarTerisi = $this->getKamarTerisi();
        $laporan = $this->getLaporanKeuanganTahunan(); // <-- ambil sekali

        return [
            'totalPenghuni' => $this->getTotalPenghuni(),
            'totalKamar' => $totalKamar,
            'kamarTerisi' => $kamarTerisi,
            'okupansi' => $this->calculateOkupansi($totalKamar, $kamarTerisi),
            'telatBayar' => $this->getTelatBayar(),

            // laporan tahunan
            'dataBulan' => $laporan['data'],
            'totalTahunan' => $laporan['total'], // <-- ini yang hilang
        ];
    }

    private function getTotalPenghuni(): int
    {
        return Penghuni::count();
    }

    private function getTotalKamar(): int
    {
        return Room::count();
    }

    private function getKamarTerisi(): int
    {
        return Room::where('status', 'terisi')->count();
    }

    private function calculateOkupansi(int $totalKamar, int $kamarTerisi): float
    {
        return $totalKamar > 0 ? round(($kamarTerisi / $totalKamar) * 100, 1) : 0;
    }

    private function getTelatBayar(): Collection
    {
        return Pembayaran::where('status', self::STATUS_BELUM_LUNAS)
            ->whereDate('periode_selesai', '<', Carbon::today())
            ->with(['penghuni', 'room'])
            ->latest()
            ->get();
    }

    private function getLaporanKeuanganTahunan(): array
    {
        $rawData = $this->getRawPemasukanData();
        $total = $this->calculateTotalTahunan($rawData);

        return [
            'data' => $this->formatLaporanBulanan($rawData),
            'total' => $total,
        ];
    }

    private function getRawPemasukanData(): array
    {
        return Pembayaran::select(
            DB::raw('EXTRACT(MONTH FROM created_at) as bulan'),
            DB::raw('SUM(jumlah) as total')
        )
            ->whereYear('created_at', now()->year)
            ->where('status', 'lunas')
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->pluck('total', 'bulan')
            ->toArray();
    }

    private function formatLaporanBulanan(array $rawData): array
    {
        return array_map(function ($index, $namaBulan) use ($rawData) {
            return [
                'nama' => $namaBulan,
                'total' => $rawData[$index + 1] ?? 0
            ];
        }, array_keys(self::BULAN_NAMES), self::BULAN_NAMES);
    }

    private function calculateTotalTahunan(array $rawData): int
    {
        return (int) array_sum($rawData);
    }
}