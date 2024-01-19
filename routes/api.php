<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\StripePaymentController;
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

//update shop account
Route::post('shopc/update_shop_details/{shopId}', [AuthenticationController::class, 'updateShopDetails']);
Route::post('archive/{id}/{status}', [AuthenticationController::class, 'archive']);


Route::middleware('auth:sanctum')->group(function() {
    //auth shop
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::get('appointments/upcoming', [AppointmentController::class, 'get_upcoming']);
    Route::get('appointments/completed', [AppointmentController::class, 'get_completed']);
    Route::post('appointments/store', [AppointmentController::class, 'store']);
    Route::delete('appointments/delete/{id}', [AppointmentController::class, 'deleteAppointment']);

    //auth user
    Route::post('logout',[AuthenticationController::class,'logout']);
    Route::get('users',[AuthenticationController::class,'users']);
    Route::get('shops',[AuthenticationController::class,'shops']);
    Route::get('client_list',[AuthenticationController::class,'clientList']);
    Route::get('shop_list',[AuthenticationController::class,'shopList']);
    Route::get('topshops',[AuthenticationController::class,'topShops']);


    Route::get('index',[AuthenticationController::class,'index']); //dont get this
    Route::post('fav',[AuthenticationController::class,'storeFavShop']);

    // //appointments
    // Route::get('appointments', [AppointmentController::class, 'index']);
    // Route::post('appointments/store', [AppointmentController::class, 'store']);

    //appointments
    Route::get('shopc/index', [ShopController::class, 'index']);
    Route::get('shopc/profile', [ShopController::class, 'profile']);
    Route::get('shopc/reviews', [ShopController::class, 'reviews']);
    Route::post('shopc/store', [ShopController::class, 'store']);
    Route::get('shopc/appointment_emergency', [ShopController::class, 'getEmergencyAppointments']);
    Route::get('shopc/appointment_get_pending', [ShopController::class, 'getAllPending']);
    Route::get('shopc/appointment_get_upcoming', [ShopController::class, 'getAllUpcoming']);
    Route::get('shopc/appointment_get_completed', [ShopController::class, 'getAllCompleted']);
    Route::get('shopc/get_completed_and_rejected', [ShopController::class, 'getCompletedRejected']);
    Route::post('shopc/appointment_accept', [ShopController::class, 'acceptAppointment']);
    Route::post('shopc/appointment_reject', [ShopController::class, 'cancelAppointment']);

    Route::post('transaction/charge', [StripePaymentController::class, 'charge']); //payment in stripe
 });
    
