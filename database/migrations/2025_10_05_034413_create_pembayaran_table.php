<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabel pembayaran (utama)
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penghuni_id')->constrained('penghuni')->onDelete('cascade');
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');

            $table->string('kode_transaksi')->unique();
            $table->decimal('jumlah', 15, 2);

            // periode fleksibel
            $table->date('periode_mulai');   // awal sewa
            $table->date('periode_selesai'); // akhir sewa

            $table->enum('status', ['menunggu_verifikasi', 'lunas', 'belum_lunas', 'ditolak', 'jatuh_tempo'])
                ->default('menunggu_verifikasi');

            $table->string('metode')->nullable(); // cash, transfer, e-wallet
            $table->text('catatan')->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // kombinasi unik agar tidak ada dobel bayar dengan periode sama persis
            $table->unique(['penghuni_id', 'periode_mulai', 'periode_selesai']);
        });


        // Tabel bukti pembayaran (upload)
        Schema::create('pembayaran_bukti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayaran')->onDelete('cascade');
            $table->string('file_path'); // path bukti bayar
            $table->string('tipe')->nullable(); // jpg, png, pdf
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Tabel riwayat verifikasi pembayaran (opsional)
        Schema::create('pembayaran_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_id')->constrained('pembayaran')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['menunggu_verifikasi', 'lunas', 'ditolak']);
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_verifikasi');
        Schema::dropIfExists('pembayaran_bukti');
        Schema::dropIfExists('pembayaran');
    }
};
