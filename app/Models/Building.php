<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
    ];

    /**
     * Relasi ke lantai
     */
    public function floors()
    {
        return $this->hasMany(Floor::class);
    }

    /**
     * Relasi ke kamar
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
