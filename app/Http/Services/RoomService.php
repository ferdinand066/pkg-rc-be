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
                return $q->orderBy('floor_id', 'asc')->orderBy('name', 'asc');
            })
            ->when((request()->paginate == "true") ?? false, function ($query){
                return $query->paginate(10)->onEachSide(1)->withQueryString();
            }, function($query){
                return $query->get();
            });
    }

    public function create($data){
        return Room::create([
            'name' => $data['name'],
            'floor_id' => $data['floor_id'],
            'capacity' => $data['capacity'],
        ]);
    }

    public function update(Room $room, $data){
        return Room::where('id', $room->id)->update([
            'name' => $data['name'],
            'floor_id' => $data['floor_id'],
            'capacity' => $data['capacity'],
        ]);
    }
}
