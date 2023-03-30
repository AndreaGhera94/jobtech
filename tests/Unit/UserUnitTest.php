<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Repositories\UserRepository;

class UserUnitTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateUser()
    {
        // Dati di prova per il nuovo utente
        $userData = [
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'username' => 'mario.rossi',
            'password' => 'password123',
        ];

        // Creazione del nuovo utente attraverso il servizio UserService
        $userRepository = new UserRepository(new User());
        $user = $userRepository->create($userData);

        // Verifica che l'utente sia stato creato correttamente
        $this->assertDatabaseHas('users', [
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'username' => 'mario.rossi',
        ]);

        // Verifica che la password sia stata salvata correttamente
        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function testUpdateUser()
    {
        // Creazione di un nuovo utente
        $userData = [
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'username' => 'mario.rossi',
            'password' => 'password123',
        ];

        // Creazione del nuovo utente attraverso il servizio UserService
        $userRepository = new UserRepository(new User());
        $user = $userRepository->create($userData);

        // Dati di prova per l'aggiornamento dell'utente
        $userData = [
            'name' => 'modify_Mario Rossi',
            'email' => 'modify_mario.rossi@example.com',
            'username' => 'modify_mario.rossi',
            'password' => 'modify_password123',
        ];

        // Aggiornamento dell'utente attraverso il repository
        $user = $userRepository->update($user->id, $userData);

        // Verifica che l'utente sia stato aggiornato correttamente
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'modify_Mario Rossi',
            'email' => 'modify_mario.rossi@example.com',
            'username' => 'modify_mario.rossi',
        ]);
    }

    public function testDeleteUser()
    {
        // Dati di prova per il nuovo utente
        $userData = [
            'name' => 'Mario Rossi',
            'email' => 'mario.rossi@example.com',
            'username' => 'mario.rossi',
            'password' => 'password123',
        ];

        // Creazione del nuovo utente attraverso il servizio UserService
        $userRepository = new UserRepository(new User());
        $user = $userRepository->create($userData);

        // Eliminazione dell'utente attraverso il repository
        $userRepository->delete($user->id);

        // Verifica che l'utente sia stato eliminato correttamente
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}