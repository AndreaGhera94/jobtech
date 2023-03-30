<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthTest extends TestCase
{
    use DatabaseTransactions;

    public function testProtectedRouteWithNoToken()
    {
        $response = $this->json('POST', '/api/logout');

        $response->assertStatus(405);
    }

    public function testProtectedRouteWithInvalidToken()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Aggiunge un carattere casuale al token per invalidarlo
        $token .= 'X';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/logout');

        $response->assertStatus(405);
    }

    public function testProtectedRouteWithValidToken()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/logout');

        $response->assertStatus(200);
    }

    public function testNoProtectedRouteAndCheckResponse()
    {
        // Creazione utente di test
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
        ]);

        // Chiamata alla API di login con JWT
        $response = $this->json('POST', '/api/login', [
            'email' => 'testuser@example.com',
            'password' => 'password',
        ]);

        // Verifica che la risposta abbia il codice HTTP 200 (OK)
        $response->assertStatus(200);

        // Verifica che la risposta contenga il token JWT e il token di refresh
        $response->assertJsonStructure([
            'user',
            'token',
            'refreshToken',
        ]);
    }
}
