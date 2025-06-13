<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $uniqueEmail = User::where('email', $request->email)->first();
        if($uniqueEmail == null){
            $create = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
            ]);

            if($create){
                $credentials = $request->only('email', 'password');
                if ($credentials){
                    $token = $create->createToken('auth_token')->plainTextToken;
                    return response()->json([
                        'status' => 200,
                        'message' => 'Registration success and login success',
                        'user' => $create,
                        'token' => $token
                    ], 200);
                }else{
                    return response()->json([
                        'status' => 401,
                        'message' => 'Registration success but login failed',
                    ], 401);
                }
            }else{
                return response()->json([
                    'status' => 401,
                    'message' => 'Registration failed',
                ], 401);
            }
        }else{
            return response()->json([
                'status' => 401,
                'message' => 'Email already registered',
            ], 401);
        }

        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => bcrypt($request->password),
        // ]);

        // return response()->json(['user' => $user], 201);
    }

     public function login(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $existingUser = User::query()->where('email', $request->email)->first();

        if(!$existingUser) {
            return response()->json([
                'status' => 401,
                'message' => 'Email not registered.',
                'user' => $request->email,
            ], 401);
        }

        if ($existingUser && Hash::check($request->password, $existingUser->password)) {
            $token = $existingUser->createToken('auth_token')->plainTextToken;
        return response()->json([
            'status' => 200,
            'message' => 'Login success',
            'user' => $existingUser,
            'token' => $token
        ], 200);

            } else {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid Password.',
                'user' => $request->email,
        ], 401);
        }
    }

    function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Logout success',
        ], 200);
    }
}