<?php

namespace App\Http\Controllers;

use App\Http\Requests\AvailabilityRequest;
use App\Models\BorrowedRoom;
use App\Models\Room;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AvailabilityController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(AvailabilityRequest $request, Room $room)
    {
        $validated = $request->validated();
        $borrowedRooms = BorrowedRoom::where([
            ['room_id', $room->id],
            ['borrowed_date', $validated['borrowing_date']],
            ['borrowed_status', '<>', 0]
        ])->when($validated['borrowed_room_id'] ?? false, function($q, $borrowedRoomId){
            $q->where('id', '<>', $borrowedRoomId);
        })->select(['start_borrowing_time', 'end_event_time'])->orderBy('start_borrowing_time')->get();

        $slots = [];

        // Initial boundary start time
        $lastEndTime = Carbon::createFromTimeString('00:00');
        $minimumBorrowingTime = env('MINIMUM_BORROWING_TIME', 30);

        foreach ($borrowedRooms as $borrowedRoom) {
            $start = Carbon::createFromFormat('H:i', $borrowedRoom->start_borrowing_time);
            $end = Carbon::createFromFormat('H:i', $borrowedRoom->end_event_time);

            $startMinutes = (int)(floor($start->minute / $minimumBorrowingTime) * $minimumBorrowingTime);
            $start->setMinutes($startMinutes);
            
            // Ceil end time to the nearest multiple of MINIMUM_BORROWING_TIME
            $endMinutes = (int)(ceil($end->minute / $minimumBorrowingTime) * $minimumBorrowingTime);
            if ($endMinutes == 60) {
                $end->addHour()->setMinutes(0)->setSeconds(0);
            } else {
                $end->setMinutes($endMinutes)->setSeconds(0);
            }

            // Add the available slot before the current borrowing starts
            if ($lastEndTime->lt($start)) {
                $slots[] = $lastEndTime->format('H:i') . ' - ' . $start->format('H:i');
            }

            // Update the last end time
            $lastEndTime = $end;
        }

        // Add the final available slot after the last borrowing ends
        $endOfDay = Carbon::createFromTimeString('23:59');
        if ($lastEndTime->lt($endOfDay)) {
            $slots[] = $lastEndTime->format('H:i') . ' - ' . $endOfDay->format('H:i');
        }

        // Return or process the available slots as needed
        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Successfully get availability time!', compact('slots'));
    }
}
