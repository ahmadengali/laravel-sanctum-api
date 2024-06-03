<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    // Register api (post, formdata)
    public function register(Request $request) {
        $request->validate([
            "name" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|confirmed"
        ]);

        //save data
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);
        return response()->json ([
            "status" => true,
            "message" => "User registered successfully"
        ]);
    }

    // Login api (post, formdata)
    public function login(Request $request) {
        //data validation
        $request->validate([
            "email" => "required|email",
            "password" => "required"
        ]);
        //to check the email value
        $user = User::where("email", $request->email)->first();

        if(!empty($user)) {
            //check the password
            if(Hash::check($request->password, $user->password)) {
                //if email &password are compatible do:
                $token = $user->createToken("myToken")->plainTextToken;

                return response()->json([
                    "status" => true,
                    "message" => "Login successful",
                    "token" => $token
                ]);
            }
            return response()->json([
                "status" => false,
                "message" => "Password didn't match"
            ]);
        }
        return response()->json([
            "status" => false,
            "message" => "Invalid Login Dstails"
        ]);
    }

    // profile api (get) (protected method)
    public function profile() {
        $data = auth()->user(); // auth helper

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "user" => $data
        ]);
    }

    // Logout api (get) (protected method)
    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            "status" => true,
            "message" => "User loged out",
        ]);
    }

}
