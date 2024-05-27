<?php

namespace App\Http\Services;

use App\Models\BorrowedRoomItem;

class BorrowedRoomItemService
{
    public function manage(string $id, array $data = null)
    {
        $borrowedRoomItems = BorrowedRoomItem::where('borrowed_room_id', $id)->get();

        foreach ($data['item_id'] as $itemId){
            BorrowedRoomItem::updateOrCreate(['item_id' => $itemId, 'borrowed_room_id' => $id], ['quantity' => 0]);
        }

        foreach ($borrowedRoomItems as $borrowedRoomItem){
            if (!in_array($borrowedRoomItem->item_id, $data['item_id'])){
                $borrowedRoomItem->delete();
            }
        }
    }
}
