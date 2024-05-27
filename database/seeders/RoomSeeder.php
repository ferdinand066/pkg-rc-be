<?php

namespace Database\Seeders;

use App\Models\Floor;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floorRoomMap = [
            "Lt. Dasar" => ["Kantin"],
            "Lantai 1" => ["St. Andreas", "St. Bartolomeus", "St. Felix", "Aula Keluarga Kudus Nazareth"],
            "Lantai 2" => ["St. Paulus", "Ruang DPH"],
            "Lantai 3" => ["St. Yohanes", "St. Matius"]
        ];

        $floors = Floor::all();

        // Create a map with $floor->name as key and $floor->id as value
        $floorMap = $floors->pluck('id', 'name')->toArray();

        foreach ($floorRoomMap as $floorName => $rooms) {
            if (isset($floorMap[$floorName])) {
                $floorId = $floorMap[$floorName];
                foreach ($rooms as $roomName) {
                    Room::insert([
                        'id' => Str::uuid(),
                        'name' => $roomName,
                        'floor_id' => $floorId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
