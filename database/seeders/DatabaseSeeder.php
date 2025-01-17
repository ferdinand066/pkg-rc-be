<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::insert([
            'id' => '9dc625f4-2057-4644-bd8c-e8c7944f1cc9',
            'name' => 'Fer',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'address' => 'Jl. asd',
            'role' => '2',
            'account_accepted_at' => now(),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        User::create([
            'name' => 'Fer',
            'email' => 'ferdinandg066@gmail.com',
            'password' => Hash::make('password'),
            'address' => 'Jl. asd',
            'role' => '1',
            'account_accepted_at' => now(),
            'email_verified_at' => now(),
        ]);

        // \App\Models\User::factory(100)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            FloorSeeder::class,
            RoomSeeder::class,
            ItemSeeder::class,
            // RoomItemSeeder::class,
            ReportReceiverSeeder::class,
        ]);
    }
}
