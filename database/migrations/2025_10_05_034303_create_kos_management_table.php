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
        /**
         * Tabel Gedung
         */
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // contoh: Gedung A
            $table->timestamps();
        });

        /**
         * Tabel Lantai
         */
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->integer('nomor_lantai'); // contoh: 1,2,3
            $table->timestamps();
        });

        /**
         * Tabel Tipe Kamar
         */
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Standard, Deluxe, VIP
            $table->decimal('harga', 12, 2); // harga per bulan
            $table->timestamps();
        });

        /**
         * Tabel Kamar (hasil auto-generate)
         */
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->foreignId('floor_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('nomor_kamar'); // contoh: A101, A102
            $table->enum('status', ['kosong', 'terisi', 'booking'])->default('kosong');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('floors');
        Schema::dropIfExists('buildings');
    }
};
