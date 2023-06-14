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
        Schema::create('genre_movie', function (Blueprint $table) {
            $table->primary(['movie_id', 'genre_id']);

            $table->unsignedBiginteger('movie_id')->unsigned();
            $table->unsignedBiginteger('genre_id')->unsigned();

            $table->foreign('movie_id')->references('id')
                ->on('movies')->onDelete('cascade');
            $table->foreign('genre_id')->references('id')
                ->on('genres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_movie');
    }
};
