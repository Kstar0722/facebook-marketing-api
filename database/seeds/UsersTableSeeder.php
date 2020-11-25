<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param  Faker  $faker
     * @return void
     */
    public function run(Faker $faker)
    {
        DB::table('users')->delete();
        DB::table('users')->insert([
            'name' => $faker->name,
            'email' => 'fbt-test@gmail.com',
            'password' => Hash::make('secret'),
        ]);
    }
}
