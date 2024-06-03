<?php

namespace App\Http\Services;

use App\Models\RoomItem;

class RoomItemService
{
    public function manage(array $data = null)
    {
        $roomItems = RoomItem::where('room_id', $data['room_id'])->get();
        $itemIds = [];

        foreach ($data['items'] as $item){
            $roomItem = RoomItem::withTrashed()
                ->where('room_id', $data['room_id'])
                ->where('item_id', $item['item_id'])
                ->first();

            if ($roomItem) {
                $roomItem->restore();
                $roomItem->update(['quantity' => $item['quantity']]);
            } else {
                // Otherwise, create a new record
                RoomItem::create([
                    'room_id' => $data['room_id'],
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $itemIds[] = $item['item_id'];
        }

        foreach ($roomItems as $roomItem){
            if (!in_array($roomItem->item_id, $itemIds)){
                $roomItem->delete();
            }
        }
    }
}
