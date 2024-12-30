<?php

namespace App\Http\Controllers;

use App\Http\Services\BorrowedRoomService;
use App\Http\Services\RoomService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, RoomService $roomService, BorrowedRoomService $borrowedRoomService)
    {
        $data = $this->getSearchAndSort();
        // $rooms = $roomService->index($data);

        // get startDate and endDate from $request, if theres no exists, set default value to today
        $date = $request->query('date', Carbon::today()->toDateString());
        $startTime = $request->query('start_time', "00:00:00");
        $endTime = $request->query('end_time', "23:59:59");

        $borrowedRooms = $borrowedRoomService->activeRequest($date . " " . $startTime, $date . " " . $endTime);

        return $this->sendResponse(Response::HTTP_OK, 'Berhasil mendapatkan schedule', compact('borrowedRooms'));
    }
}
