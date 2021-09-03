<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller{

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $user = User::where('email',$credentials['email'])->first();
        if(!$user || !Hash::check($credentials['password'], $user->password)){
            return response()->json([
                'msg' => "usuário ou senha inválidos"
            ], 401);
        }
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return response()->json([
            'token' => $token,
            'user'=> $user
        ],200);
    }

    public function register(Request $request){
        $credentials = $request->only(['name', 'email', 'password', 'type']);
        if(User::where('email',$credentials['email'])->first() || $credentials['type'] == 1){
            return response()->json([
                'msg'=>"Já Existe uma usuário com esse email"
            ], 401);
        }
        $credentials['password'] = bcrypt($credentials['password']);
        $user = User::create($credentials);
        return response()->json([
            'user'=>$user,
            'msg'=>"Usuário cadastrado com sucesso"
        ],200);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function getUserAutenticated(){
        $user = Auth::user();
        if(!$user) return response()->json(['msg'=>'Token inválido'],401);
        return response()->json([
            'user'=>$user
        ],200);
    }
}
