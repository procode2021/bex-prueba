<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::prefix("v1")->group(function(){
    Route::post('login', [AuthController::class, 'login']);
    Route::prefix("auth")->group(function(){
        Route::group(['middleware' => ['auth:sanctum']], function(){
            Route::get('user/me', [AuthController::class, 'userProfile']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });
    Route::put('update/{id}', [AuthController::class, 'userUpdate']);
    Route::delete('remove/{id}', [AuthController::class, 'userRemove']);
    Route::get('users', [AuthController::class, 'allUsers']);
    Route::post('register', [AuthController::class, 'register']);
});

