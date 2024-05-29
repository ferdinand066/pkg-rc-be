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
        $rooms = $roomService->index($data);

        // get startDate and endDate from $request, if theres no exists, set default value to today
        $startDate = $request->query('start_date', Carbon::today()->toDateString());
        $endDate = $request->query('end_date', Carbon::today()->toDateString());

        $borrowedRooms = $borrowedRoomService->activeRequest($startDate, $endDate);

        return $this->sendResponse(Response::HTTP_OK, 'Berhasil mendapatkan schedule', compact('rooms', 'borrowedRooms'));
    }
}
