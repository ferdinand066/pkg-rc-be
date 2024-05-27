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
            $table->date('borrowed_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('reason');
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
