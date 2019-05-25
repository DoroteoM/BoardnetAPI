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
                'game_id' => $faker->numberBetween(1, 20000),
                'title' => $faker->streetName,
                'link' => $faker->url,
                'average_rating' => $faker->randomFloat(1, 1, 10),
                'rank' => $faker->numberBetween(1, 10000),
                'image' => $faker->imageUrl(),
                'thumbnail' => $faker->imageUrl(100, 100),
                'year_published' => $faker->numberBetween(2000,date("Y")),
                'min_players' => $faker->numberBetween(1, 3),
                'max_players' => $faker->numberBetween(4, 8),
                'playing_time' => ($faker->numberBetween(2,12))*10
            ]);
        }
    }
}
