<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoomScheduleController extends BaseController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $floors = Floor::with('rooms')->orderBy('created_at')->get();

        return $this->sendResponse(Response::HTTP_OK, 'Berhasil mendapatkan data ruangan per lantai!', compact('floors'));
    }
}
