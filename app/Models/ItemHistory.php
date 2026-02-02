<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'item_id',
        'room_id',
        'quantity',
        'type',
        'user_id',
    ];

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function room(){
        return $this->belongsTo(Room::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
