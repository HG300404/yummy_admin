<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function register(Request $request)
    {
        if (!$request->email || !$request->password) {
            return response()->json(["status" => "error", "message" => "Please enter enough information"]);
        } else {
            $user = new User;
            $user->name = $request->input("name");
            $user->phone = $request->input("phone");
            $user->address = $request->input("address");
            $check = User::where('email', $request->email)->first();
            if ($check) {
                return ["status" => "error", "message" => "Email already exists"];
            } else {
                $user->email = $request->input("email");
            }
            $user->password = Hash::make($request->input("password"));
            $user->role = $request->input('role');
            $user->image = "";
            $user->level = 1;
            $user->coin = 0;
            $user->save();
            dd($request->all());
            return response()->json(["status" => "success", "message" => "Register successful"]);
        }
    }
    function login(Request $request)
    {
        if (!$request->email || !$request->password) {
            return response()->json(["status" => "error", "message" => "Please enter enough information"]);
        } else {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(["status" => "error", "message" => "Email is not correct"]);
            } else if (!Hash::check($request->password, $user->password)) {
                return response()->json(["status" => "error", "message" => "Password is not correct"]);
            }
            return response()->json(["status" => "success", "user" => $user]);
        }
    }
}
