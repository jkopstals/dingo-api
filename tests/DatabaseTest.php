<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\User;

class DatabaseTest extends TestCase
{
    public function testDatabaseUserTable()
    {
        $faker = Faker\Factory::create();

        $name = $faker->name;
        $email = $faker->safeEmail;
        $password = $faker->password(8);
        
        User::create(['name' => $name, 'email' => $email, 'password' => $password]);
        
        $this->seeInDatabase('users', ['name' => $name, 'email' => $email]);
        
    }
}
