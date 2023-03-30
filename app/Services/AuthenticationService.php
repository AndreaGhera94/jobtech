<?php

namespace App\Services;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticationService
{

    public function __construct()
    {
    }

    public function login($credentials)
    {
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = JWTAuth::user();
        $refreshToken = JWTAuth::fromUser($user);

        return response()->json([
            'user' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'token' => $token,
            'refreshToken' => $refreshToken
        ]);
    }

    public function logout()
    {
        JWTAuth::parseToken()->invalidate();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
}