<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowedRoom extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_id',
        'borrowed_at',
        'start_date',
        'end_time',
        'borrowed_by',
        'borrowed_status',
        'updated_by',
    ];

    public function borrowedBy(){
        return $this->belongsTo(User::class, 'borrowed_by');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function borrowedRoomItems(){
        return $this->hasMany(BorrowedRoomItem::class);
    }
}
