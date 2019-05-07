<?php

use Illuminate\Database\Seeder;
use App\Game;

class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Game::truncate();

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; ++$i)
        {
            Game::create([
                'gameid' => $faker->numberBetween(1, 20000),
                'title' => $faker->streetName,
                'link' => $faker->url,
                'averageRating' => $faker->randomFloat(1, 1, 10),
                'rank' => $faker->numberBetween(1, 10000),
                'image' => $faker->imageUrl(),
                'thumbnail' => $faker->imageUrl(100, 100),
                'yearPublished' => $faker->numberBetween(2000,date("Y")),
                'minPlayers' => $faker->numberBetween(1, 3),
                'maxPlayers' => $faker->numberBetween(4, 8),
                'playingTime' => ($faker->numberBetween(2,12))*10
            ]);
        }
    }
}
