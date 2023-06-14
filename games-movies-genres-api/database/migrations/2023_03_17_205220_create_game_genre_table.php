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
        Schema::create('game_genre', function (Blueprint $table) {
            $table->primary(['game_id', 'genre_id']);

            $table->unsignedBiginteger('game_id')->unsigned();
            $table->unsignedBiginteger('genre_id')->unsigned();

            $table->foreign('game_id')->references('id')
                ->on('games')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')
                ->on('genres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_genre');
    }
};
