<?php

namespace Database\Seeders;

use App\Models\ReportReceiver;
use Illuminate\Database\Seeder;

class ReportReceiverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReportReceiver::create([
            'email' => 'ferdinandg066@gmail.com',
            'name' => 'Ferdinand',
        ]);
    }
}
