<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Kamar\Index;
use App\Livewire\Penghuni\PenghuniManager;
use App\Livewire\Penghuni\PenghuniRegister;
use App\Livewire\Pembayaran\PembayaranManager;
use App\Livewire\Pembayaran\PembayaranForm;
use App\Livewire\Laporan\LaporanKeuangan;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::get('/kamar', Index::class)
    ->middleware(['auth', 'verified'])
    ->name('kamar.index');

Route::get('/public/daftar-penghuni', PenghuniRegister::class)
    ->name('penghuni.penghuni-register');

Route::middleware(['auth'])
    ->group(function () {
        Route::get('/penghuni', PenghuniManager::class)->name('penghuni.penghuni-manager');
    });

Route::middleware(['auth'])->group(function () {
    Route::get('/pembayaran', PembayaranManager::class)->name('pembayaran.pembayaran-manager');
});

// Route untuk Public Form (Tidak memerlukan authentication)
Route::get('/public/upload-bukti-pembayaran', PembayaranForm::class)->name('pembayaran.pembayaran-form');

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan-keuangan', LaporanKeuangan::class)
        ->name('laporan.keuangan');
});

require __DIR__ . '/auth.php';
