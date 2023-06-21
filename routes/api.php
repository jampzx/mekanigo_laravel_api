<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Disaster\DisasterController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register',[AuthenticationController::class,'register']);
Route::post('login',[AuthenticationController::class,'login']);
Route::get('/disaster',[DisasterController::class,'index']);
Route::post('/disaster/store',[DisasterController::class,'store'])->middleware('auth:sanctum');
Route::get('/disaster/edit/{id}', [DisasterController::class, 'edit'])->middleware('auth:sanctum');
Route::post('/disaster/update/{id}', [DisasterController::class, 'update']); //use post method for update because it contains image and php has limitation in file/image handling
Route::delete('/disaster/delete/{id}',[DisasterController::class,'delete'])->middleware('auth:sanctum');

