<?php

namespace App\Http\Services;

use App\Models\RoomItem;

class RoomItemService
{
    public function manage(array $data = null)
    {
        $roomItems = RoomItem::where('room_id', $data['room_id'])->get();

        foreach ($data['item_id'] as $itemId){
            RoomItem::updateOrInsert(['item_id' => $itemId, 'room_id' => $data['room_id']], []);
        }

        foreach ($roomItems as $roomItem){
            if (!in_array($roomItem->item_id, $data['item_id'])){
                $roomItem->delete();
            }
        }
    }
}
