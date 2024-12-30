<?php

namespace App\Imports;

use App\Models\BorrowedRoom;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BorrowedRoomImport implements ToModel, WithHeadingRow
{
    private $rooms; // Preloaded rooms

    // Constructor accepts dynamic data
    public function __construct($rooms)
    {
        $this->rooms = $rooms; // Store preloaded rooms
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BorrowedRoom([
            'id' => Str::uuid(),
            'room_id' => $this->rooms[trim($row['room_name'])],
            'pic_name' => $row['pic_name'],
            'pic_phone_number' => $row['pic_phone_number'],
            'capacity' => $row['capacity'],
            'event_name' => $row['event_name'],
            'borrowed_date' => $row['borrowed_date'],
            'start_borrowing_time' => $row['start_borrowing_time'],
            'start_event_time' => $row['start_event_time'],
            'end_event_time' => $row['end_event_time'],
            'description' => $row['description'],
            'borrowed_by_user_id' => '9dc625f4-2057-4644-bd8c-e8c7944f1cc9',
            'borrowed_status' => 2,
        ]);
    }
}
