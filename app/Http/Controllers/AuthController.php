<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:60',
            'email' => 'required|email|max:120|unique:users,email',
            'password' => 'required|string|min:6|max:120',
        ]);

        $user = User::create([
            'name' => $data['name'] ?? explode('@', $data['email'])[0],
            'email' => strtolower($data['email']),
            'password' => Hash::make($data['password']),
        ]);

        return response()->json($this->authPayload($user), 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', strtolower($data['email']))->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        return response()->json($this->authPayload($user));
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $this->userData($request->user())]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }

    private function authPayload(User $user): array
    {
        return [
            'token' => $user->createToken('pwa')->plainTextToken,
            'user' => $this->userData($user),
        ];
    }

    private function userData(User $user): array
    {
        return ['name' => $user->name, 'email' => $user->email];
    }
}
