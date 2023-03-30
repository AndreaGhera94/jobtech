<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUserRegisterProtectedRouteWithNoToken()
    {
        $response = $this->json('POST', '/api/register');

        $response->assertStatus(405);
    }

    public function testUserRegisterProtectedRouteWithInvalidToken()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Aggiunge un carattere casuale al token per invalidarlo
        $token .= 'X';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/register');

        $response->assertStatus(405);
    }

    public function testProtectedRouteWithValidTokenAndMissingParameter()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/register', [
            'name' => 'testuser',
            'password' => 'password'
        ]);

        $response->assertStatus(400);
    }

    public function testProtectedRouteWithValidTokenAndWrongParameterEmail()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/register', [
            'email' => 'test',
            'name' => 'testuser',
            'username' => 'testuser',
            'password' => 'password'
        ]);

        $response->assertStatus(400);
    }

    public function testProtectedRouteWithValidTokenAndCorrectParameterAndCheckResponse()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/register', [
            'email' => 'test@example.com',
            'name' => 'testuser2',
            'username' => 'testuser2',
            'password' => 'password'
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'status',
            'message',
            'user',
        ]);
    }
}
