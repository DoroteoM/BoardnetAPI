<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('link')->nullable();
            $table->integer('gameId')->nullable()->unique();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('minPlayers')->nullable();
            $table->integer('maxPlayers')->nullable();
            $table->integer('playingTime')->nullable();
            $table->boolean('isExpansion')->nullable()->default(false);
            $table->integer('yearPublished')->nullable();
            $table->decimal('bggRating')->nullable();
            $table->decimal('averageRating')->nullable();
            $table->integer('rank')->nullable();
            $table->integer('numPlays')->nullable()->default(0);
            $table->integer('rating')->nullable();
            $table->boolean('owned')->nullable()->default(false);
            $table->boolean('preOrdered')->nullable()->default(false);
            $table->boolean('previousOwned')->nullable()->default(false);
            $table->boolean('want')->nullable()->default(false);
            $table->boolean('wantToPlay')->nullable()->default(false);
            $table->boolean('wantToBuy')->nullable()->default(false);
            $table->boolean('wishList')->nullable()->default(false);
            $table->string('userComment')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
