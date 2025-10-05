<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'user_id',
        'tipe',
        'referensi_id',
        'judul',
        'pesan',
        'is_read',
    ];

    /**
     * Relasi ke user penerima notifikasi
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi dinamis ke entitas lain (pembayaran, kamar, penghuni, dll)
     * Bisa dipakai morph atau manual tergantung kebutuhan.
     * Di sini contoh sederhana pakai manual referensi_id.
     */
    public function referensi()
    {
        switch ($this->tipe) {
            case 'pembayaran':
                return $this->belongsTo(Pembayaran::class, 'referensi_id');
            case 'kamar':
                return $this->belongsTo(Kamar::class, 'referensi_id');
            case 'penghuni':
                return $this->belongsTo(Penghuni::class, 'referensi_id');
            default:
                return null;
        }
    }
}
