<?php

namespace App\Http\Services;

use App\Models\RoomItem;

class RoomItemService
{
    public function create(string $itemId, ?array $data = null)
    {
        foreach ($data as $item) {
            RoomItem::create([
                'room_id' => $item['room_id'],
                'item_id' => $itemId,
                'quantity' => $item['quantity'],
            ]);
        }
    }
}
