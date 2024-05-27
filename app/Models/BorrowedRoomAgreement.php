<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowedRoomAgreement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'borrowed_room_id',
        'agreement_status',
        'created_by',
    ];

    public function borrowedRoom(){
        return $this->belongsTo(BorrowedRoom::class);
    }

    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
