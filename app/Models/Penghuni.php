<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penghuni extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penghuni';

    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'alamat',
        'ktp',
        'perjanjian',
        'status',
        'kamar_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'verified_by',
        'verified_at',
        'catatan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
        'tanggal_keluar' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Relasi ke kamar
     */
    public function kamar()
    {
        return $this->belongsTo(Room::class, 'kamar_id');
        // atau Kamar::class kalau model kamu bernama Kamar
    }

    /**
     * Relasi ke user (yang verifikasi)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relasi ke riwayat verifikasi
     */
    public function verifikasiRiwayat()
    {
        return $this->hasMany(PenghuniVerifikasi::class, 'penghuni_id');
    }
}
