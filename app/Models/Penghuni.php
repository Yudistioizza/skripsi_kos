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
        'room_id', // Fixed: sesuai migrasi
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
     * Relasi ke kamar/room
     */
    public function kamar()
    {
        return $this->belongsTo(Room::class, 'room_id');
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

    /**
     * Accessor untuk status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'yellow',
            'aktif' => 'green',
            'ditolak' => 'red',
            'keluar' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Accessor untuk status label
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'aktif' => 'Aktif',
            'ditolak' => 'Ditolak',
            'keluar' => 'Keluar',
            default => ucfirst($this->status),
        };
    }

    public function scopeHasActiveRoom($query)
    {
        return $query->whereNotNull('room_id')
            ->whereHas('kamar', fn($q) => $q->whereNotNull('room_type_id'));
    }
}