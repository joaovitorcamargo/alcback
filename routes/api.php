<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController
};
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login',[AuthController::class, 'login'])->name('auth.login');
Route::post('auth/register',[AuthController::class, 'register'])->name('auth.register');

Route::middleware(['apiJwt'])->group(function () {
    Route::get('getUsers',[AuthController::class,'getUsers']);
    Route::get('getAuthUser',[AuthController::class,'getUserAutenticated']);
});
