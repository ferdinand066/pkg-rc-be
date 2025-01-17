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

        $searchFields = ['event_name', 'pic_name'];

        return BorrowedRoom::with('borrowedRoomItems.item', 'borrowedBy', 'room.floor')
            ->when($orderBy, function ($q, $orderBy) use ($dataOrder) {
                return $q->orderBy($orderBy, $dataOrder ?? 'asc');
            }, function ($q) {
                return $q->orderBy('borrowed_date', 'asc')
                ->orderBy('start_borrowing_time', 'asc');
            })
            ->when($isUser ?? true, function($q){
                return $q->where('borrowed_by_user_id', Auth::user()->id);
            })
            ->whereRaw("CONCAT(borrowed_date, ' ', end_event_time) > ?", [now()])
            ->when($search ?? false, function($q) use ($search, $searchFields){
                $q->where(function ($query) use ($search, $searchFields) {
                    foreach ($searchFields as $field) {
                        $query->orWhere($field, 'like', '%' . $search . '%');
                    }

                    $query->orWhereHas('room', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when((request()->paginate == "true") ?? false, function ($query){
                return $query->paginate(10)->onEachSide(1)->withQueryString();
            }, function($query){
                return $query->get();
            });
    }

    public function activeRequest($startDate, $endDate){
        // check borrowedRoom between startDate and endDate and borrowed_status in 1 and 2
        return BorrowedRoom::with('borrowedRoomItems.item', 'borrowedBy', 'room')
            // ->whereBetween('borrowed_date', [$startDate, $endDate])
            ->whereRaw("CONCAT(borrowed_date, ' ', start_borrowing_time) >= ?", [$startDate])
            ->whereRaw("CONCAT(borrowed_date, ' ', end_event_time) <= ?", [$endDate])
            ->whereIn('borrowed_status', [1, 2])
            ->orderBy('start_borrowing_time', 'asc')
            ->get();
    }

    public function create($data){
        $conflictingBookings = BorrowedRoom::where('borrowed_status', '<>', 0)
            ->where('room_id', $data['room_id'])
            ->where('borrowed_date', $data['borrowed_date'])
            ->where(function($query) use ($data) {
                $query->whereBetween('start_borrowing_time', [$data['start_borrowing_time'], $data['end_event_time']])
                    ->orWhereBetween('end_event_time', [$data['start_borrowing_time'], $data['end_event_time']])
                    ->orWhere(function($query) use ($data) {
                        $query->where('start_borrowing_time', '<=', $data['start_borrowing_time'])
                            ->where('end_event_time', '>=', $data['end_event_time']);
                    });
            })
            ->exists();

        if ($conflictingBookings) {
            throw new ConflictHttpException('Ruangan ini sudah dipesan pada jam yang dipilih.');
        }

        return BorrowedRoom::create([
            'room_id' => $data['room_id'],
            'pic_name' => $data['pic_name'],
            'pic_phone_number' => $data['pic_phone_number'],
            'capacity' => $data['capacity'],
            'event_name' => $data['event_name'],
            'borrowed_date' => $data['borrowed_date'],
            'start_borrowing_time' => $data['start_borrowing_time'],
            'start_event_time' => $data['start_event_time'],
            'end_event_time' => $data['end_event_time'],
            'description' => $data['description'],
            'borrowed_by_user_id' => Auth::user()->id,
        ]);
    }

    public function update(BorrowedRoom $borrowedRoom, $data){
        $conflictingBookings = BorrowedRoom::where('borrowed_status', '<>', 0)
            ->where('id', '<>', $borrowedRoom->id)
            ->where('room_id', $data['room_id'])
            ->where('borrowed_date', $data['borrowed_date'])
            ->where(function($query) use ($data) {
                $query->whereBetween('start_borrowing_time', [$data['start_borrowing_time'], $data['end_event_time']])
                    ->orWhereBetween('end_event_time', [$data['start_borrowing_time'], $data['end_event_time']])
                    ->orWhere(function($query) use ($data) {
                        $query->where('start_borrowing_time', '<=', $data['start_borrowing_time'])
                            ->where('end_event_time', '>=', $data['end_event_time']);
                    });
            })
            ->exists();

        if ($conflictingBookings) {
            throw new ConflictHttpException('Ruangan ini sudah dibook pada jam yang dipilih.');
        }

        return BorrowedRoom::where('id', $borrowedRoom->id)->update([
            'room_id' => $data['room_id'],
            'pic_name' => $data['pic_name'],
            'pic_phone_number' => $data['pic_phone_number'],
            'capacity' => $data['capacity'],
            'event_name' => $data['event_name'],
            'borrowed_date' => $data['borrowed_date'],
            'start_borrowing_time' => $data['start_borrowing_time'],
            'start_event_time' => $data['start_event_time'],
            'end_event_time' => $data['end_event_time'],
            'description' => $data['description'],
        ]);
    }

    public function updateStatus(BorrowedRoom $borrowedRoom, $data){
        return BorrowedRoom::where('id', $borrowedRoom->id)->update([
            'borrowed_status' => $data['borrowed_status'],
        ]);
    }

    public function declineOtherRequest(BorrowedRoom $borrowedRoom){
        return BorrowedRoom::where('id', '<>', $borrowedRoom->id)
            ->where('room_id', $borrowedRoom->room_id)
            ->where('borrowed_date', $borrowedRoom->borrowed_date)
            ->where(function($query) use ($borrowedRoom) {
                $query->whereBetween('start_borrowing_time', [$borrowedRoom->start_borrowing_time, $borrowedRoom->end_event_time])
                    ->orWhereBetween('end_event_time', [$borrowedRoom->start_borrowing_time, $borrowedRoom->end_event_time])
                    ->orWhere(function($query) use ($borrowedRoom) {
                        $query->where('start_borrowing_time', '<=', $borrowedRoom->start_borrowing_time)
                            ->where('end_event_time', '>=', $borrowedRoom->end_event_time);
                    });
            })
            ->where('borrowed_status', 1)
            ->update([
                'borrowed_status' => 0
            ]);
    }
}
