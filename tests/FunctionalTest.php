<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    
    use DatabaseMigrations; //refresh to empty database
    
    private $tokenMissingCode = 400;
    private $noContentCode = 204;


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
    public function testCreateUserAndAuthSuccess()
    {
        $faker = Faker\Factory::create();

        $name = $faker->name;
        $email = $faker->safeEmail;
        $password = $faker->password(8);
        
        
        $this->json('POST', '/api/users', ['name' => $name, 'email' => $email, 'password' => $password, 'password_confirmation' => $password])
            ->seeJsonStructure(['data' => ['id']]);
        
        $this->seeInDatabase('users', ['email' => $email]);
        
        $request = $this->call('POST', '/api/auth', ['email' => $email, 'password' => $password]);
        $request = json_decode($request->getContent(), true);
        $this->assertArrayHasKey('token', $request);
        $token = $request['token'];
        
        $this->json('GET', '/api/validate-token?token='.$token)->assertResponseStatus($this->noContentCode);
        
        $request = json_decode($this->call('GET', '/api/users/me', ['token' => $token])->getContent(), true);
        $this->assertArrayHasKey('data', $request);
        $id = $request['data']['id'];
        
        $request = json_decode($this->call('GET', '/api/users/'.$id, ['token' => $token])->getContent(), true);
        $this->assertArrayHasKey('email', $request['data']);
        $this->assertEquals($email, $request['data']['email']);
        
        $this->json('GET', '/api/users?token='.$token)->seeJsonStructure(['data' => ['*' => ['id','name','email','created_at','updated_at']], 'meta']);
        
        $this->json('DELETE', '/api/users/'.(int)$id.'?token='.$token)->assertResponseStatus($this->noContentCode);
        
    }
    
    public function testUsersReportAuthError() 
    {
        $this->json('GET','/api/users')->assertResponseStatus($this->tokenMissingCode);
    }
    
    public function testUsersMeReportAuthError() 
    {
        $this->json('GET','/api/users/me')->assertResponseStatus($this->tokenMissingCode);
    }

    public function testUsers1ReportAuthError()
    {
        $this->json('GET','/api/users/1')->assertResponseStatus($this->tokenMissingCode);
    }
    
    public function testUsers1UpdateReportAuthError()
    {
        $this->json('POST','/api/users/1', ['name' => 'Should Fail'])->assertResponseStatus($this->tokenMissingCode);
    }
    
    public function testUsers1DeleteReportAuthError()
    {
        $this->json('DELETE','/api/users/1')->assertResponseStatus($this->tokenMissingCode);
        
    }


}
