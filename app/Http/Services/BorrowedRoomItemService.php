<?php

namespace App\Http\Services;

use App\Models\BorrowedRoom;
use App\Models\BorrowedRoomItem;

class BorrowedRoomItemService
{
    public function manage(string $id, array $data = null)
    {
        $borrowedRoomItems = BorrowedRoomItem::where('borrowed_room_id', $id)->get();
        $itemIds = [];

        foreach ($data['items'] as $item){
            $borrowedRoomItem = BorrowedRoomItem::withTrashed()
                ->where('borrowed_room_id', $id)
                ->where('item_id', $item['item_id'])
                ->first();

            if ($borrowedRoomItem) {
                $borrowedRoomItem->restore();
                $borrowedRoomItem->update(['quantity' => $item['quantity']]);
            } else {
                // Otherwise, create a new record
                BorrowedRoomItem::create([
                    'borrowed_room_id' => $id,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $itemIds[] = $item['item_id'];
        }

        foreach ($borrowedRoomItems as $borrowedRoomItem){
            if (!in_array($borrowedRoomItem->item_id, $itemIds)){
                $borrowedRoomItem->delete();
            }
        }
    }
}
