<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if($validator->fails()) {
            return $this->sendError("Validation error", $validator->errors(), 400);
        }

        $credentials = $validator->validated();

        // get user
        $user = User::where('email', $credentials['email'])->first();
        if(!$user) {
            return $this->sendError('Email has not been registered', null, 400);
        }

        // compare password
        if(!Hash::check($credentials['password'], $user->password)) {
            return $this->sendError('Email or password not match', null, 400);
        }

        $token = auth()->attempt($credentials);
        if(!$token) {
            return $this->sendError('Unauthorized', null, 401);
        }

        $response = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token
        ];

        return $this->sendResponse("Login successful", $response);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'password_confirmation' => 'required|string',
        ]);

        if($validator->fails()) {
            return $this->sendError("Validation error", $validator->errors(), 400);
        }

        $credentials = $validator->validated();

        User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password'])
        ]);

        return $this->sendResponse('User registered successfully');
    }

    public function logout()
    {
        auth()->invalidate(true);
        auth()->logout();
        return response()->json([
            'message' => 'Successfully log out'
        ], 200);
    }
}
