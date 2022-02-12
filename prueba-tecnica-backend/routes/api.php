<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

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


Route::post('login', [UserController::class,'login']);
Route::post('registerAdmin', [UserController::class,'registerAdmin']); 
Route::group(['middleware' => ['jwt.verify']], function () {   
    Route::post('user', [UserController::class,'register']); 
    
    Route::get('logout', [UserController::class,'logout']);
    Route::get('user', [UserController::class,'getAll']);
    Route::get('user/me', [UserController::class,'getAuthUser']);
    Route::put('user/{id}', [UserController::class,'update']);    
    Route::get('user/{id}', [UserController::class,'get']);
    Route::delete('user/{id}', [UserController::class,'destroy']);
    Route::get('product/{id}', [ProductController::class,'get']);
    Route::get('product', [ProductController::class,'getAll']);
    Route::get('product/{id}', [ProductController::class,'get']);
    Route::post('product', [ProductController::class,'store']);
    Route::put('product/{id}', [ProductController::class,'update']);
    Route::delete('product/{id}', [ProductController::class,'destroy']);
});
