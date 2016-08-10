<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DatabaseTest extends TestCase
{
    public function testDatabase()
    {
        $this->seeInDatabase('users', ['email' => 'jk@jk.jk']);
    }
}
