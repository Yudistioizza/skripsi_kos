<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranBukti extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_bukti';

    protected $fillable = [
        'pembayaran_id',
        'file_path',
        'tipe',
        'uploaded_by',
    ];

    // Relasi ke Pembayaran
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id');
    }

    // Relasi ke User (yang upload)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
