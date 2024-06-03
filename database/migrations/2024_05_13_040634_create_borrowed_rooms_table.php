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
            $table->string('pic_name');
            $table->string('pic_phone_number');
            $table->integer('capacity');
            $table->string('event_name');
            $table->date('borrowed_date');
            $table->time('start_borrowing_time');
            $table->time('start_event_time');
            $table->time('end_event_time');
            $table->text('description');
            $table->foreignUuid('borrowed_by_user_id')->constrained('users');
            $table->tinyInteger('borrowed_status')->default(1)->comment('0: canceled, 1: pending, 2: accepted');
            $table->timestamps();
            $table->softDeletes();
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
