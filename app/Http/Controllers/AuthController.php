<?php

namespace App\Http\Controllers;

use App\Libs\Bcrypt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    //

    public function register(Request $req) {

        $fields =  $req->validate([
            'name' => "required|max:255",
            'email' => "required|email|unique:users",
            'password' => "required|confirmed",

        ]);
        $bcrypt = new Bcrypt();
        $fields['password'] = $bcrypt->hash($fields['password']);

        $user = User::create($fields);

        $token = $user->createToken($fields['email']);

        return ['user' => $user, 'token' => $token->plainTextToken];
    }

    public function login(Request $req) {

        $fields =  $req->validate([
            'email' => "required|email|exists:users",
            'password' => "required",
        ]);
        $bcrypt = new Bcrypt();
        $user = User::where('email', $fields['email'])->first();
        $isCorrectPassword = $bcrypt->compare($fields['password'], $user->password);
        if(!$user || !$isCorrectPassword) {
            return [
                'message' => 'Invalid Credentials',
            ];
        }
        $token = $user->createToken($user->email);

        return ['user' => $user, 'token' => $token->plainTextToken];      
    }

    public function logout(Request $req) {

        // one way to access headers : Authorization]
        
        // $token = $req->headers->get('Authorization');
        $bearerToken = request()->bearerToken();

        $token = PersonalAccessToken::findToken($bearerToken);

        if (!$token) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
        $req->user()->tokens()->delete();
        return [
                'message' => 'You are logged out',
            ];
        }
}
