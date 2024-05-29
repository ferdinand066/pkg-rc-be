<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Room;
use App\Models\RoomItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();
        $items = Item::all();

        foreach ($rooms as $room) {
            // Get a random number of items between 3 and 5
            $randomItems = $items->random(rand(3, 5));

            foreach ($randomItems as $item) {
                // Create a new RoomItem record
                RoomItem::create([
                    'room_id' => $room->id,
                    'item_id' => $item->id,
                ]);
            }
        }
    }
}
