<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Project;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectTest extends TestCase
{
    use DatabaseTransactions;

    public function testAddProjectProtectedRouteWithNoToken()
    {
        $response = $this->json('POST', '/api/projects');

        $response->assertStatus(405);
    }

    public function testAddProjectProtectedRouteWithInvalidToken()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Aggiunge un carattere casuale al token per invalidarlo
        $token .= 'X';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/projects');

        $response->assertStatus(405);
    }

    public function testAddProjectProtectedRouteWithValidTokenAndWrongParameter()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/projects', [
            'email' => 'test',
            'name' => 'testuser',
            'username' => 'testuser',
            'password' => 'password'
        ]);

        $response->assertStatus(400);
    }

    public function testAddProjectProtectedRouteWithValidTokenAndValidParameter()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('POST', '/api/projects', [
            'title' => 'test',
            'description' => 'test',
            'status' => 'opened'
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'slug',
                'status',
                'tasks_count',
                'completed_tasks_count'
            ]
        ]);
    }

    public function testGetProjectsListProtectedRouteWithNoToken()
    {
        $response = $this->json('GET', '/api/projects');

        $response->assertStatus(405);
    }

    public function testGetProjectsListProtectedRouteWithInvalidToken()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Aggiunge un carattere casuale al token per invalidarlo
        $token .= 'X';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('GET', '/api/projects');

        $response->assertStatus(405);
    }

    public function testGetProjectsListProtectedRouteWithValidTokenAndWrongParameter()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('GET', '/api/projects');

        $response->assertStatus(400);
    }

    public function testGetProjectsListProtectedRouteWithValidTokenAndValidParameter()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('GET', '/api/projects', [
            'page' => '1',
            'perPage' => '3',
            'sortBy' => 'alpha_asc',
            'withClosed' => '0',
            'onlyClosed' => '0'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'slug',
                    'status',
                    'tasks_count',
                    'completed_tasks_count'
                ],
            ]
        ]);
    }

    public function testGetProjectProtectedRouteWithNoToken()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $response = $this->json('GET', '/api/projects/'.$project->id);

        $response->assertStatus(405);
    }

    public function testGetProjectProtectedRouteWithInvalidToken()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Aggiunge un carattere casuale al token per invalidarlo
        $token .= 'X';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('GET', '/api/projects/'.$project->id);

        $response->assertStatus(405);
    }

    public function testGetProjectProtectedRouteWithValidToken()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('GET', '/api/projects/'.$project->id);

        $response->assertStatus(200);
    }

    public function testModifyProjectProtectedRouteWithNoToken()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $response = $this->json('PATCH', '/api/projects/'.$project->id);

        $response->assertStatus(405);
    }

    public function testModifyProjectProtectedRouteWithInvalidToken()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Aggiunge un carattere casuale al token per invalidarlo
        $token .= 'X';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('PATCH', '/api/projects/'.$project->id);

        $response->assertStatus(405);
    }

    public function testModifyProjectProtectedRouteWithValidTokenAndWrongParameter()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            ])->json('PATCH', '/api/projects/'.$project->id, [
                'title' => 'test',
            ]);

        $response->assertStatus(400);
    }

    public function testModifyProjectProtectedRouteWithValidTokenAndValidParameter()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            ])->json('PATCH', '/api/projects/'.$project->id, [
                'title' => 'test',
                'description' => 'test',
            ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'slug',
                'status',
                'tasks_count',
                'completed_tasks_count'
            ]
        ]);
    }

    public function testModifyProjectStatusProtectedRouteWithNoToken()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $response = $this->json('PATCH', '/api/projects/'.$project->id.'/open');

        $response->assertStatus(405);
    }

    public function testModifyProjectStatusProtectedRouteWithInvalidToken()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        // Aggiunge un carattere casuale al token per invalidarlo
        $token .= 'X';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->json('PATCH', '/api/projects/'.$project->id.'/open');

        $response->assertStatus(405);
    }

    public function testModifyProjectStatusProtectedRouteWithValidTokenAndWrongParameter()
    {
        // crea un progetto
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            ])->json('PATCH', '/api/projects/'.$project->id.'/test');

        $response->assertStatus(400);
    }

    public function testModifyProjectStatusProtectedRouteWithValidTokenAndValidParameter()
    {
        // crea un progetto
        $project = Project::factory()->create();
        $status_from = $project->status;

        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            ])->json('PATCH', '/api/projects/'.$project->id.'/open');

        if($status_from == 'closed')
            $response->assertStatus(400);
        else
            $response->assertStatus(204);
    }
}
