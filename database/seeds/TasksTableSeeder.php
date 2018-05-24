<?php

use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
      App\Task::truncate();

      $faker = \Faker\Factory::create();

      // And now, let's create a few articles in our database:
      for ($i = 0; $i < 5; $i++) {
         App\Task::create([
              'title' => $faker->sentence,
              'description' => $faker->paragraph,
          ]);
      }
    }
}
