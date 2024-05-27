<?php

namespace App\Http\Services;

use App\Models\BorrowedRoom;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class BorrowedRoomService
{
    public function index(array $data = null)
    {
        if ($data !== null) extract($data);
        $isUser = Auth::user()->role === 1;

        return BorrowedRoom::with('borrowedRoomItems.item', 'borrowedBy', 'room')
            ->when($orderBy, function ($q, $orderBy) use ($dataOrder) {
                return $q->orderBy($orderBy, $dataOrder ?? 'asc');
            }, function ($q) {
                return $q->orderBy('borrowed_date', 'asc');
            })
            ->when($isUser ?? true, function($q){
                return $q->where('borrowed_by_user_id', Auth::user()->id);
            })
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();
    }

    public function create($data){
        $conflictingBookings = BorrowedRoom::where('borrowed_status', 2)
            ->where('room_id', $data['room_id'])
            ->where('borrowed_date', $data['borrowed_date'])
            ->where(function($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function($query) use ($data) {
                        $query->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($conflictingBookings) {
            throw new ConflictHttpException('The room is already booked for the selected time slot.');
        }

        return BorrowedRoom::create([
            'room_id' => $data['room_id'],
            'borrowed_date' => $data['borrowed_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'reason' => $data['reason'],
            'borrowed_by_user_id' => Auth::user()->id,
        ]);
    }

    public function update(BorrowedRoom $borrowedRoom, $data){
        $conflictingBookings = BorrowedRoom::where('borrowed_status', 2)
            ->where('id', '<>', $borrowedRoom->id)
            ->where('room_id', $data['room_id'])
            ->where('borrowed_date', $data['borrowed_date'])
            ->where(function($query) use ($data) {
                $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                    ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                    ->orWhere(function($query) use ($data) {
                        $query->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    });
            })
            ->exists();

        if ($conflictingBookings) {
            throw new ConflictHttpException('The room is already booked for the selected time slot.');
        }

        return BorrowedRoom::where('id', $borrowedRoom->id)->update([
            'room_id' => $data['room_id'],
            'borrowed_date' => $data['borrowed_date'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'reason' => $data['reason'],
        ]);
    }

    public function updateStatus(BorrowedRoom $borrowedRoom, $data){
        return BorrowedRoom::where('id', $borrowedRoom->id)->update([
            'borrowed_status' => $data['borrowed_status'],
        ]);
    }
}
