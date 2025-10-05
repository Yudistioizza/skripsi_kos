<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = ['nama'];

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function getRoomCountByStatusAttribute()
    {
        return [
            'kosong' => $this->rooms()->where('status', 'kosong')->count(),
            'terisi' => $this->rooms()->where('status', 'terisi')->count(),
            'booking' => $this->rooms()->where('status', 'booking')->count(),
        ];
    }
}