<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel Penghuni
        Schema::create('penghuni', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique()->nullable();
            $table->string('no_hp')->nullable();
            $table->string('alamat')->nullable();
            $table->string('ktp')->nullable(); // file path upload KTP
            $table->string('perjanjian')->nullable(); // file path perjanjian kos
            $table->enum('status', ['menunggu_verifikasi', 'aktif', 'ditolak', 'keluar'])->default('menunggu_verifikasi');

            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null');

            $table->timestamp('tanggal_masuk')->nullable();
            $table->timestamp('tanggal_keluar')->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Tabel Penghuni Verifikasi (riwayat verifikasi)
        Schema::create('penghuni_verifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penghuni_id')->constrained('penghuni')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['menunggu_verifikasi', 'aktif', 'ditolak', 'keluar']);
            $table->text('catatan')->nullable();
            $table->timestamp('verified_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penghuni_verifikasi');
        Schema::dropIfExists('penghuni');
    }
};
