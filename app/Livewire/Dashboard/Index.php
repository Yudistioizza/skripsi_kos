<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Penghuni;
use App\Models\Room;
use App\Models\Pembayaran;
use Carbon\Carbon;

class Index extends Component
{
    public function render()
    {
        // 1. Ringkasan
        $totalPenghuni = Penghuni::count();
        $totalKamar = Room::count();
        $kamarTerisi = Room::where('status', 'terisi')->count();
        $okupansi = $totalKamar ? round(($kamarTerisi / $totalKamar) * 100, 1) : 0;

        // 2. Keterlambatan = yang statusnya belum_lunas DAN periode_selesai sudah lewat hari ini
        $today = Carbon::today();
        $telatBayar = Pembayaran::where('status', 'belum_lunas')
            ->whereDate('periode_selesai', '<', $today)
            ->with(['penghuni', 'room'])
            ->latest()
            ->get();

        return view('livewire.dashboard.index', compact(
            'totalPenghuni',
            'totalKamar',
            'kamarTerisi',
            'okupansi',
            'telatBayar'
        ));
    }
}
