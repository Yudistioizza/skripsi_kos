<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_verifikasi';

    protected $fillable = [
        'pembayaran_id',
        'verified_by',
        'status',
        'catatan',
        'verified_at',
    ];

    protected $dates = ['verified_at'];

    // Relasi ke Pembayaran
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id');
    }

    // Relasi ke User (yang verifikasi)
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
