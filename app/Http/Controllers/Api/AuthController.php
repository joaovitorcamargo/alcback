<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Mail\newMailAlc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
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
        Mail::send(new newMailAlc($user->name, $credentials['email'], "você fez um novo login em Alc Sistemas"));
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
        Mail::send(new newMailAlc($credentials['name'], $credentials['email'], "Seu usuario foi criado no Alc Sistemas"));
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

    public function getUsers(){
        $users = User::where('type', '=' , 2)->get();
        return response()->json([
            'users'=>$users
        ],200);
    }
    public function getUserById($id){
        $user = User::where('id', '=' , $id)->first();
        return response()->json([
            'user' => $user
        ],200);
    }

    public function editUser(Request $request){
        $user = User::find($request->id);
        $user->update([
            'name'=> $request->name,
            'email' => $request->email
        ]);
         Mail::send(new newMailAlc($request->name, $request->email, "Seu usuario foi editado no Alc Sistemas"));
        return response()->json([
            'msg'=>'Usuário editado com sucesso',
            'user' => $user
        ],200);
    }
    public function deleteUser(Request $request){
        $user = User::find($request->id);
        Mail::send(new newMailAlc($user->name, $user->email, "Seu usuario foi removido no Alc Sistemas"));
        $user->delete();
        return response()->json([
            'msg'=>'Usuário removido com sucesso'
        ],200);
    }
}
