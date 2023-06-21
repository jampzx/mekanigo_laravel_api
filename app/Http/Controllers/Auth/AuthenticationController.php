<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
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

    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        $credentials['verified'] = 1; 
        return $credentials;
    }
}
