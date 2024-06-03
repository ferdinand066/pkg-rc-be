<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Http\Services\RoomItemService;
use App\Http\Services\RoomService;
use App\Models\Room;
use Symfony\Component\HttpFoundation\Response;

class RoomController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(RoomService $service)
    {
        $data = $this->getSearchAndSort();
        $rooms = $service->index($data);

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan data ruangan!', compact('rooms'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRoomRequest $request, RoomService $service, RoomItemService $roomItemService)
    {
        $validated = $request->validated();
        $room = $service->create($validated);

        $roomItemPayload = [
            'room_id' => $room->id,
            'items' => $validated['items'],
        ];

        $roomItemService->manage($roomItemPayload);
        $room->load('roomItems.item');

        return $this->sendResponse(Response::HTTP_CREATED, 'Berhasil membuat data ruangan', compact('room'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        $room->load('items');

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mendapatkan data ruangan', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, Room $room, RoomService $service, RoomItemService $roomItemService)
    {
        $validated = $request->validated();

        $service->update($room, $validated);

        $roomItemPayload = [
            'room_id' => $room->id,
            'items' => $validated['items'],
        ];

        $roomItemService->manage($roomItemPayload);
        $room->load('roomItems.item');

        return $this->sendResponse(Response::HTTP_ACCEPTED, 'Berhasil mengubah data ruangan', compact('room'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        $room->delete();

        return $this->sendResponse(Response::HTTP_OK, 'Berhasil menghapus data ruangan', []);
    }
}
