<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Mail\newMailAlc;
use App\Models\Task;
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
    
    public function registerTask(Request $request){
        $credentials = $request->only(['name', 'done_date', 'status']);
        $task = Task::create([
            'name'=>$credentials['name'],
            'done_date'=>$credentials['done_date'],
            'status'=>$credentials['status'],
        ]);
        $task->users()->attach(Auth::user()->id);
        return response()->json([
            'task' => $task,
            'msg'=>"Task cadastrado com sucesso"
        ],200);
    }

    public function getTasks(){
        $tasks = Task::all();
        return response()->json([
            'tasks' => $tasks
        ],200);
    }

    public function editTask(Request $request){
        $task = Task::find($request->id);
        $task->update([
            'name'=> $request->name,
            'due_date' => $request->due_date,
            'statusn' => $request->status
        ]);
        return response()->json([
            'msg'=>'Task editada com sucesso',
        ],200);
    }
    public function getTaskById($id){
        $task = Task::where('id', '=' , $id)->first();
        return response()->json([
            'task' => $task
        ],200);
    }
    public function removeTask(Request $request){
        $task = Task::find($request->id);
        $task->users()->detach(Auth::user()->id);
        $task->delete();
        return response()->json([
            'msg'=>'Usuário removido com sucesso'
        ],200);
    }
}
