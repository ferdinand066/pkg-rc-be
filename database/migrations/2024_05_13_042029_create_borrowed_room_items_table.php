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
        Schema::create('borrowed_room_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('borrowed_room_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('item_id')->constrained();
            $table->integer('quantity');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowed_room_items');
    }
};
