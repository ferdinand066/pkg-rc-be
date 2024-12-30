<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BorrowedRoom extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'room_id',
        'pic_name',
        'pic_phone_number',
        'capacity',
        'event_name',
        'borrowed_date',
        'start_borrowing_time',
        'start_event_time',
        'end_event_time',
        'description',
        'borrowed_by_user_id',
        'borrowed_status',
    ];

    public function startBorrowingTime(): Attribute {
        // check if value format is H:i:s, format to H:i, else just make the same
        return Attribute::make(
            get: fn (string $value) => DateTime::createFromFormat('H:i:s', $value)->format('H:i'),
            set: fn (string $value) => $this->formatTime($value),
        );
    }

    public function startEventTime(): Attribute {
        // check if value format is H:i:s, format to H:i, else just make the same
        return Attribute::make(
            get: fn (string $value) => DateTime::createFromFormat('H:i:s', $value)->format('H:i'),
            set: fn (string $value) => $this->formatTime($value),
        );
    }

    public function endEventTime(): Attribute {
        return Attribute::make(
            get: fn (string $value) => DateTime::createFromFormat('H:i:s', $value)->format('H:i'),
            set: fn (string $value) => $this->formatTime($value),
        );
    }

    public function room(){
        return $this->belongsTo(Room::class);
    }

    public function borrowedBy(){
        return $this->belongsTo(User::class, 'borrowed_by_user_id');
    }

    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function borrowedRoomItems(){
        return $this->hasMany(BorrowedRoomItem::class);
    }

    public function borrowedRoomAgreements(){
        return $this->hasMany(BorrowedRoomAgreement::class);
    }

    /**
     * Format time input.
     *
     * @param string $value
     * @return string
     */
    private function formatTime(string $value): string {

        // If input is already in H:i:s format, return it directly
        if (DateTime::createFromFormat('H:i:s', $value)) {
            return $value;
        }

        // If input is in H:i format, convert it to H:i:s
        if (DateTime::createFromFormat('H:i', $value)) {
            return DateTime::createFromFormat('H:i', $value)->format('H:i:s');
        }

        if (DateTime::createFromFormat('H.i.s', $value)) {
            return DateTime::createFromFormat('H.i.s', $value)->format('H:i:s');
        }

        // Invalid format - throw an exception or return default (optional)
        throw new \InvalidArgumentException('Invalid time format. Expected H:i or H:i:s.');
    }
}
