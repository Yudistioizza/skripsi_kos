<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'floor_id',
        'room_type_id',
        'nomor_kamar',
        'status',
    ];

    /**
     * Relasi ke gedung
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Relasi ke lantai
     */
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Relasi ke tipe kamar
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
