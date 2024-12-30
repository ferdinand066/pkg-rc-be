<?php

namespace App\Console\Commands;

use App\Imports\BorrowedRoomImport;
use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportBorrowedRoom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:room';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import borrowed rooms data from an Excel file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = "public/import/booking.xlsx";

        $storagePath = Storage::path($filePath); // Maps storage/app/{file}

        // Check if the file exists
        if (!Storage::exists($filePath)) {
            $this->error('File not found: ' . $filePath);
            return;
        }

        // Preload Room data (key: name, value: uuid)
        $this->info('Preloading room data...');
        $rooms = Room::pluck('id', 'name');

        $this->info('Starting import...');
        try {
            // Import Excel data using the preloaded rooms
            Excel::import(new BorrowedRoomImport($rooms), $storagePath);
            $this->info('Import completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error during import: ' . $e->getMessage());
        }
    }
}
