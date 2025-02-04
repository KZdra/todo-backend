<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
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

Route::group([ 'middleware' => 'api', 'prefix' => 'auth'], function ($router) {

    Route::post('register', [AuthController::class,'register']);
    Route::post('login', [AuthController::class,'login']);
    Route::post('logout', [AuthController::class,'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);


    // Route::post('todos', [TodoController::class,'getTodo']);
    Route::get('todos', [TodoController::class, 'getTodos']);
    Route::post('todos', [TodoController::class, 'store']);
    Route::put('todos/{id}', [TodoController::class, 'update']);
    Route::delete('todos/{id}', [TodoController::class, 'destroy']);
});
