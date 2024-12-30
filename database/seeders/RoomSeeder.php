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
            "Akita" => ["Aula Akita", "Kapel Akita"],
            "Lt. Dasar" => ["Kantin", "Area Kristus Raja", "Sekretariat"],
            "Lantai 1" => ["Gereja RC", "Aula KKN", "St. Andreas", "St. Bartolomeus", "St. Filipus", "St. Felix", "St. Yakobus"],
            "Lantai 2" => ["Ruang DPH", "Kapel HK Yesus dan Maria", "St. Paulus", "St. Petrus", ],
            "Lantai 3" => ["St. Gabriel", "St. Matius", "St. Yohanes", "St. Lukas"],
            "Lain Lain" => ["Lain Lain"]
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
                        'capacity' => 50,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
