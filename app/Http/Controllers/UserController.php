<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class UserController extends Controller
{
     function register(Request $request)
    {
        if (!$request->email) {
            return response()->json(["status" => "error", "message" => "Email is required"]);
        }

        $check = User::where('email', $request->email)->first();
        if ($check) {
            return response()->json(["status" => "error", "message" => "Email already exists"]);
        }

        $user = new User;
        $user->name = $request->input("name");
        $user->phone = $request->input("phone") ?? '';
        $user->address = $request->input("address") ?? '';
        $user->email = $request->input("email");
        $user->password = Hash::make($request->input("password") ?? '');
        $user->role = $request->input('role') ?? 'user';
        $user->image = $request->input('image') ?? '';
$user->level = 1;
        $user->coin = 0;
        $user->save();

        return response()->json(["status" => "success", "message" => "Đăng ký thành công", "user" => $user]);
    }

    function login(Request $request)
    {
        if (!$request->email) {
            return response()->json(["status" => "error", "message" => "Enter missing information"]);
 }
    
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(["status" => "error", "message" => "Email does not exist"]);
        }
    
        // Check if the request is from Google login
        if ($request->isGoogleLogin) {
            return response()->json(["status" => "success", "user" => $user]);
        }
    
        if (!$request->password || !Hash::check($request->password, $user->password)) {
            return response()->json(["status" => "error", "message" => "Wrong password"]);
        }
    
        return response()->json(["status" => "success", "user" => $user]);
    }
    


    function getAll(Request $request)
    {
        $users = User::query();
        $users = $users->get();
        return response()->json($users);

    }

    function getUser(string $id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }

    function update(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);

            if ($request->name) {
                $user->name = $request->input("name");
            }
            if ($request->password) {
                $user->password = Hash::make($request->input("password"));
            }
            if ($request->email) {
                $user->email = $request->input("email");
            }
            if ($request->phone) {
                $user->phone = $request->input("phone");
            }
            if ($request->address) {
                $user->address = $request->input("address");
            }
            if ($request->role) {
                $user->role = $request->input("role");
            }

            if ($request->level) {
                $user->level = $request->input("level");
            }
            if ($request->coin) {
                $user->coin = $request->input("coin");
            }
            $user->update();

            return response()->json(['status' => "SUCCESS", "data" => $user]);



        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => "error", 'message' => 'ID không tồn tại']);
        }

    }
    function delete(string $id)
    {
        $user = User::where('id', $id)->first();
        if (!$user) {
            return response()->json(["status" => "error", "message" => "ID không tồn tại"]);
        } else {
            $user->delete();
            return response()->json(["status" => "success", "message" => "Xoá thành công"]);
        }
    }

    function search(string $input)
    {
        if (empty($input)) {
            return ["status" => "error", 'message' => 'Not input to search'];
        } else {
            $results = User::where(function ($query) use ($input) {
                $columns = Schema::getColumnListing('users');
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $input . '%');
                }
            })->get();
        
            if ($results->isEmpty()) {
                return ["status" => "success", 'message' => 'Not infor'];
            } else {
                return $results;
            }
        }


    }
}
