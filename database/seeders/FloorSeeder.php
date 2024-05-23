<?php

namespace Database\Seeders;

use App\Models\Floor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floors = ["Lt. Dasar", "Lantai 1", "Lantai 2", "Lantai 3"];
        
        foreach ($floors as $floor) {
            Floor::insert([
                'id' => Str::uuid(),
                'name' => $floor,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
