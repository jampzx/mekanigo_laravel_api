<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\UpdateShopRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\RegisterShopRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Appointment;
use App\Models\PasswordReset as ModelsPasswordReset;
use App\Models\Shops;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;



class AuthenticationController extends Controller
{

    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();
    
        if (!$user || !$user->email) {
            return response()->error('No Record Found', 'Incorrect Email Address Provided', 404);
        }
    
        $resetPasswordToken = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    
        if (!$userPassReset = ModelsPasswordReset::where('email', $user->email)->first()) {
            ModelsPasswordReset::create([
                'email' => $user->email,
                'token' => $resetPasswordToken
            ]);
        } else {
            $userPassReset->update([
                'email' => $user->email,
                'token' => $resetPasswordToken
            ]);
        }
    
        // Update the mail configuration before sending the notification
        Config::set('mail.to.address', $user->email);
        Config::set('mail.to.name', $user->name); // Assuming you have a 'name' attribute in your User model
    
        $user->notify(
            new ResetPasswordNotification($resetPasswordToken)
        );
    
        return new JsonResponse(['message' => 'A Code has been sent to your Email Address.'],201);
    }

    public function reset(ResetPasswordRequest $request)
    {
        $attributes = $request->validated();

        $user = User::where('email', $attributes['email'])->first();

        if(!$user){
            //return response()->error('No Record Found', 'Incorrect Email Address Provided',404);
            return response([
                'message' => 'No Record Found', 'Incorrect Email Address Provided'
            ],404);
        }

        $resetRequest = ModelsPasswordReset::where('email', $user->email)->first();

        if(!$resetRequest || $resetRequest->token != $request->token){
            //return response()->error('An Error Occured. Please Try Again', 'Token Mismatched.',400);
            return response([
                'message' => 'Your code is incorrect', 'Code Mismatched.'
            ],400);
        }

        $user->fill([
            'password' => Hash::make($attributes['password']),
        ]);
        $user->save();

        $user->tokens()->delete();

        $resetRequest->delete();

        $token = $user->createToken('authToken')->plainTextToken;

        // $loginResponse=[
        //     'user'=>UserResource::make($user),
        //     'token'=>$token
        // ];

        // return response()->success(
        //     $token,
        //     'Password Reset Success',201
        // );
        return response([
            $token,
            'message' => 'Password Reset Successful'
        ],201);
        
    }

    public function users()
    {
        //get all users
        $user_list = User::where('user_type', '!=', 'admin')
                     ->orderBy('name', 'desc')
                     ->get();
    
        //get all client count
        $clients = User::where('user_type', '=', 'user')
                     ->orderBy('name', 'desc')
                     ->get();
    
        $total_clients = $clients->count();

        //get all shop count
        $shops = User::where('user_type', '=', 'shop')
                     ->orderBy('name', 'desc')
                     ->get();
    
        $total_shops = $shops->count();

    
    
        return response([
            'data' => $user_list,
            'total_clients'=> $total_clients,
            'total_shops' => $total_shops
        ], 200);
    }

    public function shopList()
    {
        // Get all shops along with their reviews
        $shop_list = User::where('user_type', '=', 'shop')
                        ->orderBy('name', 'desc')
                        ->get();

        $total_shop_list = $shop_list->count();
    
        return response([
            'data' => $shop_list,
            'total_shop_list' => $total_shop_list
        ], 200);
    }
    
    public function clientList()
    {
        //get all users
        $clients = User::where('user_type', '=', 'user')
                     ->orderBy('name', 'desc')
                     ->get();

        $total_clients = $clients->count();
    
        return response([
            'data' => $clients,
            'total_clients' => $total_clients
        ], 200);
    }

    public function shops()
    {
        // Get all shops along with their reviews
        $shop_list = User::where('user_type', '=', 'shop')
                        ->orderBy('name', 'desc')
                        ->with('reviews_for_shop')
                        ->get();
    
        return response([
            'data' => $shop_list
        ], 200);
    }
    


    public function topShops()
    {
        // Get the top 5 shops with the highest average rating from the reviews table
        $shop_list = User::where('user_type', '=', 'shop')
                        ->with('reviews_for_shop')
                        ->withCount('reviews')
                        ->withAvg('reviews', 'ratings')
                        ->orderByDesc('reviews_avg_ratings')
                        ->take(5)
                        ->get();

        return response([
            'data' => $shop_list
        ], 200);
    }


    public function registerUser(RegisterUserRequest $request)
    {
        $request->validated();
        $userData = [
            'name'=>$request->name,
            'email'=>$request->email,
            'phone_number'=>$request->phone_number,
            'age'=>$request->age,
            'address'=>$request->address,
            'user_type'=>$request->user_type,
            'password'=>Hash::make($request->password),
        ];

        
        $user = User::create($userData);
        $token = $user->createToken('mekanigo')->plainTextToken;

        return response([
            'user'=>$user,
            'token'=>$token
        ],201);
    }

    public function registerShop(RegisterShopRequest $request)
    {
        $request->validated();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads', $filename, 'public');
        }

        $userData = [
            'name'=>$request->name,
            'email'=>$request->email,
            'phone_number'=>$request->phone_number,
            'address'=>$request->address,
            'user_type'=>$request->user_type,
            'open_close_time'=>$request->open_close_time,
            'open_close_date'=>$request->open_close_date,
            'latitude'=>$request->latitude,
            'longitude'=>$request->longitude,
            'filename' => $filename,
            'path' => $path,
            'services' =>$request->services,
            'password'=>Hash::make($request->password),
        ];
        
        $user = User::create($userData);
        $token = $user->createToken('mekanigo')->plainTextToken;

        return response([
            'user'=>$user,
            'token'=>$token
        ],201);
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        $user = User::whereEmail($request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password))
        {
            return response([
                'message'=>'Invalid credentials'
            ],422);    
              
        }

        $token = $user->createToken('donite')->plainTextToken;

        return response([
            'user'=>$user,
            'token'=>$token
        ],200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
    
        return response([
            'message' => 'Logged out successfully'
        ], 200);
    }

    //dont get this
    public function index()
    {
        $user = array(); //this will return a set of user and shop data
        $user = Auth::user();
        $shop = User::where('user_type', 'shop')->get();
        $details = $user->user_details;
        $shopData = Shops::all();
        //this is the date format without leading
        $date = now()->format('Y-m-d'); //change date format to suit the format in database

        //make this appointment filter only status is "upcoming"
        $appointment = Appointment::where('status', 'upcoming')->where('date', $date)->first();

        //collect user data and all shop details
        foreach($shopData as $data){
            //sorting shop name and shop details
            foreach($shop as $info){
                if($data['shop_id'] == $info['id']){
                    $data['shop_name'] = $info['name'];
                    $data['shop_profile'] = $info['path'];
                    if(isset($appointment) && $appointment['shop_id'] == $info['id']){
                        $data['appointments'] = $appointment;
                    }
                }
            }
        }

        $user['shop'] = $shopData;
        $user['details'] = $details; //return user details here together with shop list

        return response()->json([
            'data'=>$user,
            'date'=>$date
        ], 200);
    }

    public function storeFavShop(Request $request)
    {

        $saveFav = UserDetails::where('user_id',Auth::user()->id)->first();

        $docList = json_encode($request->get('favList'));

        //update fav list into database
        $saveFav->fav = $docList;  //and remember update this as well
        $saveFav->save();

        return response()->json([
            'success'=>'The Favorite List is updated',
        ], 200);
    }

    public function updateShopDetails(UpdateShopRequest $request, $shopId)
    {
        $request->validated();
    
        $shop = User::findOrFail($shopId);
    
        $updateData = [
            'name' => $request->filled('name') ? $request->name : $shop->name,
            'email' => $request->filled('email') ? $request->email : $shop->email,
            'phone_number' => $request->filled('phone_number') ? $request->phone_number : $shop->phone_number,
            'address' => $request->filled('address') ? $request->address : $shop->address,
            'open_close_time' => $request->filled('open_close_time') ? $request->open_close_time : $shop->open_close_time,
            'open_close_date' => $request->filled('open_close_date') ? $request->open_close_date : $shop->open_close_date,
            'latitude' => $request->filled('latitude') ? $request->latitude : $shop->latitude,
            'longitude' => $request->filled('longitude') ? $request->longitude : $shop->longitude,
        ];
    
        // Update basic details
        $shop->update($updateData);
    
        // Update image if provided
        if ($request->hasFile('image')) {
            // Delete the previous image file
            Storage::disk('public')->delete($shop->path);
    
            // Upload and update new image
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('uploads', $filename, 'public');
    
            $shop->update([
                'filename' => $filename,
                'path' => $path,
            ]);
        }
    
        return response(['message' => 'Shop details updated successfully'], 201);
    }

    public function archive($id,$status)
    {
        $user = User::findOrFail($id);
    
        $user->update([
            'status' => $status,
        ]);
    
        return response([
            'message' => 'The user was updated successfully'
        ], 201);
    }
    


    // public function update(UpdateUserRequest $request, $id)
    // {
    //     $request->validated();
    //     $user = User::findOrFail($id);

    //     $userData = [
    //         'verified' => $request->verified,
    //     ];

    //     $user->update($userData);
    //     return response([
    //         'message' => 'The user was updated successfully'
    //     ],201);
    // }

}
