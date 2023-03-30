<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function register($credentials)
    {
        $user = $this->userRepository->create($credentials);

        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating user',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User created succesfully',
            'user' => [
                'email' => $user->email,
                'name' => $user->name,
            ]
        ], 201);
    }
}