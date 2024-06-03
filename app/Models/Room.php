<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'floor_id',
        'capacity',
    ];

    public function floor(){
        return $this->belongsTo(Floor::class);
    }

    public function roomItems(){
        return $this->hasMany(RoomItem::class);
    }

    public function borrows(){
        return $this->hasMany(BorrowedRoom::class);
    }
}
