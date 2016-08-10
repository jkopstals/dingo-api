<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{

    /**
     * Test that an invalid request cannot authenticate.
     *
     * @return void
     */
    public function testInvalidAuthFailure()
    {
        $this->json('POST', '/api/auth') //no data passed
            ->assertResponseStatus('401');

        $this->json('POST', '/api/auth', ['email' => 'invalid', 'password' => 'invalid'])
            ->assertResponseStatus('401');

        $this->json('POST', '/api/auth', ['email' => 'jk@jk.jk'])
            ->assertResponseStatus('401');

        $this->json('POST', '/api/auth', ['email' => 'jk@jk.jk', 'password' => 'invalid'])
            ->assertResponseStatus('401');
    }

    /**
     * Test that the default seeded user can authenticate and receive a token
     *
     * @return void
     */
    public function testSeededUserAuthTokenSuccess()
    {
        $this->json('POST', '/api/auth', ['email' => 'jk@jk.jk', 'password' => 'password'])
            ->seeJsonStructure(['token']);
    }

    /**
     * Test that the default seeded user can use his token to access restricted pages
     *
     * @return void
     */
    public function testSeededUserAuthorizedPageSuccess()
    {
        $request = $this->call('POST', '/api/auth', ['email' => 'jk@jk.jk', 'password' => 'password']);
        $request = json_decode($request->getContent(), true);
        $this->json('GET', '/api/users?token='.$request['token'])->seeJsonStructure(['data' => ['*' => ['id','name','email','created_at','updated_at']], 'meta']);
        $this->json('GET', '/api/users/me?token='.$request['token'])->seeJson(['email' => 'jk@jk.jk']);
        $this->json('GET', '/api/users/1?token='.$request['token'])->seeJsonStructure(['data' => ['id','name','email','created_at','updated_at']]);
    }


}
