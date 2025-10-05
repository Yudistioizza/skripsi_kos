<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'pembayaran';

    protected $fillable = [
        'penghuni_id',
        'room_id',
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

    protected $casts = [
        'jumlah' => 'decimal:2',
        'periode_mulai' => 'date',
        'periode_selesai' => 'date',
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_transaksi)) {
                $model->kode_transaksi = 'PAY-' . strtoupper(uniqid());
            }
        });
    }

    public function penghuni(): BelongsTo
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function bukti(): HasMany
    {
        return $this->hasMany(PembayaranBukti::class);
    }

    public function verifikasi(): HasMany
    {
        return $this->hasMany(PembayaranVerifikasi::class);
    }

    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status', 'menunggu_verifikasi');
    }

    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    public function scopeJatuhTempo($query)
    {
        return $query->where('status', 'jatuh_tempo');
    }
}