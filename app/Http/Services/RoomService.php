<?php

namespace App\Http\Services;

use App\Models\Room;
use App\Models\User;
use DateInterval;
use DateTime;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class RoomService
{
    public function index(array $data = null)
    {
        if ($data !== null) extract($data);

        return Room::with('floor', 'roomItems.item')->when($orderBy, function ($q, $orderBy) use ($dataOrder) {
                return $q->orderBy($orderBy, $dataOrder ?? 'asc');
            }, function ($q) {
                return $q->orderBy('name', 'asc');
            })
            ->paginate(10)
            ->onEachSide(1)
            ->withQueryString();
    }

    public function create($data){
        return Room::create([
            'name' => $data['name'],
            'floor_id' => $data['floor_id'],
        ]);
    }

    public function update(Room $room, $data){
        return $room->update([
            'name' => $data['name'],
            'floor_id' => $data['floor_id'],
        ]);
    }
}
