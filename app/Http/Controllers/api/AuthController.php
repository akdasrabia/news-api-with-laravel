<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Response;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "fail",'error' => $validator->errors()], 422);
        } else {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
            ]);

            return response()->json([
                "status" => "success",
                "message" => "User registration successful."
            ], 201);
        }

    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(["status" => "fail",'error' => $validator->errors()], 422);
        } else {
            $user = User::where('email', $request->email)->first();
            if ( $user &&  Hash::check($request->password, $user->password)) {

                return response()->json([
                    "status" => "success",
                    "token" => $user->createToken("mysecretkey")->plainTextToken,
                    "message" => "Login successful.",
                    "user" => $user
                ], 200);
            }else {
                return response()->json([
                    "status" => "fail",
                    "message" => "Email or Password incorrect"
                ], 404);
            }
        }
    }

    public function logout()
    {

        if (Auth::check()) {

            auth()->user()->currentAccessToken()->delete();
            return response()->json([
                "status" => "success",
                "message" => "User logged out successfully"
            ], 200);
        }
        return response()->json([
            "status" => "fail",
            "message" => "You do not have permission to access this resource. Please log in again with correct credentials or sign up."
        ], 401);
    }
}
