<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowedRoomItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'borrowed_room_id',
        'quantity',
    ];

    public function borrowedRoom(){
        return $this->belongsTo(BorrowedRoom::class);
    }

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
