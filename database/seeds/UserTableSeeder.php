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
            'password' => 'password', //no need for bcrypt() since mutator is used
        ]);
        factory(User::class, 50)->create();
    }
}
