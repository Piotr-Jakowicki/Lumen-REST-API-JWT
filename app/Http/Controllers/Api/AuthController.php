<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6|max:20'
        ];

        $validated = $this->validate($request, $rules);

        $user = User::create(array_merge($validated, ['password' => Hash::make($validated['password'])]));

        $token = Auth::login($user);

        return $this->respondWithToken($token);
    }

    public function login(Request $request){
        $rules = [
            'email' => 'required',
            'password' => 'required'
        ];

        $validated = $this->validate($request, $rules);

        if(!$token = Auth::attempt($validated)){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }
}
