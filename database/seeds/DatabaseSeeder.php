<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        \DB::table('users')->insert([
            0 => [
                'role'      => 'reviewer',
                'name'      => 'Gloria', 
                'user_name' => 'gloria-dtec',
                'email'     => 'gloria@gmail.com',
                'password'  => bcrypt('&oS&942#')
            ]
        ]);
    }
}
