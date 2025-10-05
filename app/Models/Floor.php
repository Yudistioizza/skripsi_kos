<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'nomor_lantai',
    ];

    /**
     * Relasi ke gedung
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Relasi ke kamar
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
