<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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

Route::group(['prefix' => '/user'], function () {

    Route::post('/register', [UserController::class, 'store']);

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::get('/me', [UserController::class, 'me']);

        Route::delete('/{id}', [UserController::class, 'destroy']);
    });
});


Route::group(['prefix' => 'auth'], function () {

    Route::post('/login', [AuthController::class, 'store']);

    Route::group(['middleware' => ['auth:sanctum']], function () {

        Route::delete('/logout', [AuthController::class, 'destroy']);
    });
});
