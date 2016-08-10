<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Janis Kopstals',
            'email' => 'jk@jk.jk',
            'password' => bcrypt('password'),
        ]);
        factory(User::class, 50)->create();
    }
}
