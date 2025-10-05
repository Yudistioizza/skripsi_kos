<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran';

    protected $fillable = [
        'penghuni_id',
        'room_id', // ganti dari kamar_id ke room_id
        'kode_transaksi',
        'jumlah',
        'periode_mulai',
        'periode_selesai',
        'status',
        'metode',
        'catatan',
        'verified_by',
        'verified_at',
    ];

    protected $dates = [
        'periode_mulai',
        'periode_selesai',
        'verified_at',
    ];

    // Relasi ke Penghuni
    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class, 'penghuni_id');
    }

    // Relasi ke Room (kamar)
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id'); // ganti dari kamar_id ke room_id
    }

    // Relasi ke User (yang verifikasi)
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Relasi ke Bukti Pembayaran
    public function bukti()
    {
        return $this->hasMany(PembayaranBukti::class, 'pembayaran_id');
    }

    // Relasi ke Riwayat Verifikasi
    public function riwayatVerifikasi()
    {
        return $this->hasMany(PembayaranVerifikasi::class, 'pembayaran_id');
    }
}
