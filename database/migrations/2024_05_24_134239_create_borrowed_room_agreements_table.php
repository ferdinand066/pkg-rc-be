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
        Schema::create('borrowed_room_agreements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('borrowed_room_id')->constrained();
            $table->tinyInteger('agreement_status')->comment('0: declined, 1: accepted');
            $table->foreignUuid('created_by_user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowed_room_agreements');
    }
};
