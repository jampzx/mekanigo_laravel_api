<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{

    public function users()
    {
        //get all users
        $users = User::where('user_type', '!=', 'admin')
                     ->orWhereNull('user_type')
                     ->orderBy('name', 'desc')
                     ->get();
    
        $total_users = $users->count();

        //get all verified users
        $verified_users = User::where(function ($query) {
            $query->where('user_type', '!=', 'admin')
                  ->orWhereNull('user_type');
        })
        ->where('verified', 1)
        ->orderBy('name', 'desc')
        ->get();
        $verified_users = $verified_users->count();

        //get all unverified users
        $unverified_users = User::where(function ($query) {
            $query->where('user_type', '!=', 'admin')
                  ->orWhereNull('user_type');
        })
        ->where('verified', 0)
        ->orderBy('name', 'desc')
        ->get();
        $unverified_users = $unverified_users->count();

    
    
        return response([
            'data' => $users,
            'total_users' => $total_users,
            'verified_users'=> $verified_users,
            'unverified_users' => $unverified_users
        ], 200);
    }

    public function register(RegisterRequest $request)
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
            'filename' => $filename,
            'path' => $path,
            'verified' => false,
            'password'=>Hash::make($request->password),
        ];

        
        $user = User::create($userData);
        $token = $user->createToken('donite')->plainTextToken;

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
    
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['verified'] = 1; 
        return $credentials;
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $request->validated();
        $user = User::findOrFail($id);

        $userData = [
            'verified' => $request->verified,
        ];

        $user->update($userData);
        return response([
            'message' => 'The user was updated successfully'
        ],201);
    }

}
