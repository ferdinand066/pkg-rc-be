<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrowed_rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('room_id')->constrained();
            $table->date('borrowed_at');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignUuid('borrowed_by')->constrained('users');
            $table->tinyInteger('borrowed_status')->comment('1: active, 2: accepted, 0: deleted');
            $table->timestamps();
            $table->foreignUuid('updated_by')->nullable()->default(null)->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowed_rooms');
    }
};
