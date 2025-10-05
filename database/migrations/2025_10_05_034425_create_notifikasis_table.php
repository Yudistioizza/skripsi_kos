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
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();

            // user penerima notifikasi (admin atau penghuni)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            // tipe notifikasi: kamar, penghuni, pembayaran, dll
            $table->string('tipe')->nullable();

            // id referensi (misalnya id pembayaran, kamar, atau penghuni terkait)
            $table->unsignedBigInteger('referensi_id')->nullable();

            // isi notifikasi
            $table->string('judul');
            $table->text('pesan')->nullable();

            // status sudah dibaca atau belum
            $table->boolean('is_read')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};
