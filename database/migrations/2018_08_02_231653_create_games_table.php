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
            $table->integer('bgg_game_id')->nullable()->unique();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('min_players')->nullable();
            $table->integer('max_players')->nullable();
            $table->integer('playing_time')->nullable();
            $table->boolean('is_expansion')->nullable()->default(false);
            $table->integer('year_published')->nullable();
            $table->decimal('bgg_rating')->nullable();
            $table->decimal('average_rating')->nullable();
            $table->integer('rank')->nullable();
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
