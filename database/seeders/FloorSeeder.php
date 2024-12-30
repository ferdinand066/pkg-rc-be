<?php

namespace Database\Seeders;

use App\Models\Floor;
use Illuminate\Database\Seeder;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floors = ["Akita", "Lt. Dasar", "Lantai 1", "Lantai 2", "Lantai 3", "Lain Lain"];

        foreach ($floors as $floor) {
            Floor::insert([
                'name' => $floor,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
