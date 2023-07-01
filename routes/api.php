<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Disaster\DisasterController;
use App\Http\Controllers\Donation\DonationController;
use App\Http\Controllers\StripePaymentController;
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

//login registration users
Route::post('register',[AuthenticationController::class,'register']);
Route::post('login',[AuthenticationController::class,'login']);
Route::post('logout',[AuthenticationController::class,'logout'])->middleware('auth:sanctum');
Route::get('users',[AuthenticationController::class,'users'])->middleware('auth:sanctum');
Route::put('/user/update/{id}',[AuthenticationController::class,'update'])->middleware('auth:sanctum');

//disasters
Route::get('/disaster',[DisasterController::class,'index'])->middleware('auth:sanctum');
Route::post('/disaster/store',[DisasterController::class,'store'])->middleware('auth:sanctum');
Route::get('/disaster/edit/{id}', [DisasterController::class, 'edit'])->middleware('auth:sanctum');
Route::post('/disaster/update/{id}', [DisasterController::class, 'update'])->middleware('auth:sanctum'); //use post method for update because it contains image and php has limitation in file/image handling
Route::delete('/disaster/delete/{id}',[DisasterController::class,'delete'])->middleware('auth:sanctum');

//donations
Route::post('donation/charge', [StripePaymentController::class, 'charge']); //payment in stripe
Route::get('donation',[DonationController::class,'index'])->middleware('auth:sanctum');
Route::post('/donation/store',[DonationController::class,'store'])->middleware('auth:sanctum');
Route::get('/donation/edit/{id}', [DonationController::class, 'edit'])->middleware('auth:sanctum');
Route::put('/donation/update/{id}', [DonationController::class, 'update'])->middleware('auth:sanctum'); 
Route::delete('/donation/delete/{id}',[DonationController::class,'delete'])->middleware('auth:sanctum');
