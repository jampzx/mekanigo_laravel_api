<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ShopController;
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
Route::post('register/user',[AuthenticationController::class,'registerUser']);
Route::post('register/shop',[AuthenticationController::class,'registerShop']);
Route::post('login',[AuthenticationController::class,'login']);

//reset password
Route::post('forgot',[AuthenticationController::class,'forgot']);
Route::post('reset',[AuthenticationController::class,'reset']);



Route::middleware('auth:sanctum')->group(function() {
    //auth shop
    Route::get('shop', [AppointmentController::class, 'index']);
    Route::post('shop/store', [AppointmentController::class, 'store']);

    //auth user
    Route::post('logout',[AuthenticationController::class,'logout']);
    Route::get('users',[AuthenticationController::class,'users']);
    Route::get('index',[AuthenticationController::class,'index']);
    Route::post('fav',[AuthenticationController::class,'storeFavShop']);

    //appointments
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::post('appointments/store', [AppointmentController::class, 'store']);

    //appointments
    Route::get('shopc/index', [ShopController::class, 'index']);
    Route::post('shopc/store', [ShopController::class, 'store']);

});
