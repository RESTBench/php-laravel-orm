<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Http\Response as HttpResponse;
use App\User;

class AuthenticationTest extends TestCase
{

    use DatabaseMigrations, DatabaseTransactions;

    public function testLoginWithWrongData()
    {
        $data = ['email' => 'email', 'password' => 'password'];
        $response = $this->call('POST', '/api/login', $data);

        $this->assertEquals(HttpResponse::HTTP_UNAUTHORIZED, $response->status());
    }

    public function testLoginWithUserRegistred()
    {
        $password = 'resttest';
        $data = [
            'name'      => 'RestTest',
            'email'     => 'resttest@email.com',
            'password'  => bcrypt($password),
        ];

        User::create($data);

        $response = $this->call('POST', '/api/login', [
            'email' => $data['email'], 
            'password' => $password
        ]);

        $this->assertEquals(HttpResponse::HTTP_OK, $response->status());
        $content = json_decode($response->getContent());
        $this->assertObjectHasAttribute('token', $content);
        $this->assertNotEmpty($content->token);
    }

    public function testSholdReturnErrorToCreateToken()
    {
        Tymon\JWTAuth\Facades\JWTAuth::shouldReceive('attempt')
            ->once()
            ->andThrow('Tymon\JWTAuth\Exceptions\JWTException', "could_not_create_token");

        $data = ['email' => 'email', 'password' => 'password'];
        $response = $this->call('POST', '/api/login', $data);

        $this->assertEquals(HttpResponse::HTTP_INTERNAL_SERVER_ERROR, $response->status());

    }
}
