<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create([
            'name' => 'Anderson Moraes',
            'email' => 'anderson@ycloud.com.br',
            'password' => bcrypt('secret'),
            'created_at' => Carbon::now(),
        ]);
    }
}
