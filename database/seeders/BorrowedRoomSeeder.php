<?php

namespace Database\Seeders;

use App\Models\BorrowedRoom;
use App\Models\BorrowedRoomAgreement;
use App\Models\BorrowedRoomItem;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BorrowedRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::with('roomItems')->get();
        $admins = User::where('role', 2)->get();
        $users = User::where('role', 1)->get();

        $faker = Faker::create();

        foreach ($rooms as $room) {
            // Randomly decide the number of events (3 to 5) for each room
            $numberOfEvents = $faker->numberBetween(5, 8);
            $previousEndTime = Carbon::today()->setTime(8, 0)->addMinutes($faker->numberBetween(0, 60));

            for ($i = 0; $i < $numberOfEvents; $i++) {
                // Set the start borrowing time
                if ($i === 0) {
                    $startBorrowingTime = $previousEndTime;
                } else {
                    $startBorrowingTime = $previousEndTime->addMinutes($faker->numberBetween(30, 60));
                }

                $startEventTime = $startBorrowingTime->copy()->addMinutes(30);
                $endEventTime = $startBorrowingTime->copy()->addHour();

                $borrowedStatus = $faker->randomElement([0, 2]);
                $borrowedRoom = BorrowedRoom::create([
                    'room_id' => $room->id,
                    'pic_name' => $faker->firstName(),
                    'pic_phone_number' => $faker->phoneNumber(),
                    'capacity' => $faker->numberBetween(20, 200),
                    'event_name' => $faker->realText(30),
                    'borrowed_date' => Carbon::today()->toDateString(),
                    'start_borrowing_time' => $startBorrowingTime->format('H:i'),
                    'start_event_time' => $startEventTime->format('H:i'),
                    'end_event_time' => $endEventTime->format('H:i'),
                    'description' => $faker->realText(200),
                    'borrowed_by_user_id' => $users->random()->id,
                    'borrowed_status' => $borrowedStatus,
                ]);

                $roomItems = $room->roomItems->shuffle();
                $selectedItems = $roomItems->take($faker->numberBetween(0, $roomItems->count()));

                foreach ($selectedItems as $item) {
                    BorrowedRoomItem::create([
                        'borrowed_room_id' => $borrowedRoom->id,
                        'item_id' => $item->item_id,
                        'quantity' => $faker->numberBetween(1, $item->quantity) // Adjust quantity as needed
                    ]);
                }

                foreach ($admins as $admin) {
                    BorrowedRoomAgreement::create([
                        'borrowed_room_id' => $borrowedRoom->id,
                        'created_by_user_id' => $admin->id,
                        'agreement_status' => $borrowedStatus === 2 ? 1 : 0,
                    ]);
                }

                // Update the previous end time for the next iteration
                $previousEndTime = $endEventTime;
            }
        }
    }
}
