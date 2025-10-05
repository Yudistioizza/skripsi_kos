<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PenghuniVerifikasi extends Model
{
    use HasFactory;

    protected $table = 'penghuni_verifikasi';

    protected $fillable = [
        'penghuni_id',
        'verified_by',
        'status',
        'catatan',
        'verified_at',
    ];

    protected $dates = [
        'verified_at',
    ];

    /**
     * Relasi ke penghuni
     */
    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    /**
     * Relasi ke user (yang verifikasi)
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
