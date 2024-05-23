<?php

namespace App\Http\Controllers;
use App\Models\Restaurants;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    function create(Request $request)
    {
        if (!$request->name || !$request->address || !$request->phone || !$request->opening_hours || $request->user_id) {
            return response()->json(["status" => "error", "message" => "Not enough infor"]);
        } else {
            $item = new Restaurants;

            $check = Restaurants::where('name', $request->name)->first();
            if ($check) {
                return ["status" => "error", "message" => "Name exist"];
            } else {
                $item->name = $request->input("name");
            }
            $item->phone = $request->input("phone");
            $item->address = $request->input("address");
            $item->opening_hours = $request->input('opening_hours');
            $item->user_id = $request->input('user_id');
            $item->save();            
            return response()->json(["status" => "success", "message" => "Add new restaurant success"]);
        }
    }

    function getAll(Request $request)
    {
        $list = Restaurants::query();
        $list = $list->get();
        return response()->json($list);

    }

    function getItem(string $id)
    {
        try {
            $item = Restaurants::findOrFail($id);
            return response()->json($item);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID not exist']);
        }

    }

    function update(Request $request)
    {
        try {
            $item = Restaurants::findOrFail($request->id);

            if ($request->name) {
                $item->name = $request->input("name");
            }
            if ($request->address) {
                $item->address = $request->input("address");
            }
            if ($request->phone) {
                $item->phone = $request->input("phone");
            }
            if ($request->opening_hours) {
                $item->opening_hours = $request->input("opening_hours");
            }
            if ($request->owner_name && $request->user_id){
                $user = User::findOrFail($request->user_id);
                $user->name = $request->input("owner_name");
                $user->update();
            }
            $item->update();

            return response()->json(['status' => "SUCCESS", "data" => $item]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => "error", 'message' => 'ID not exist']);
        }

    }
    function delete(string $id)
    {
        $item = Restaurants::where('id', $id)->first();
        if (!$item) {
            return response()->json(["status" => "error", "message" => "ID not exist"]);
        } else {
            $item->delete();
            return response()->json(["status" => "success", "message" => "Delete success"]);
        }
    }

    function search(string $input)
    {
        if (empty($input)) {
            return ["status" => "error", 'message' => 'Enter input to'];
        } else {
            $results = Restaurants::where(function ($query) use ($input) {
                $columns = Schema::getColumnListing('restaurants');
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $input . '%');
                }
            })->get();
        
            if ($results->isEmpty()) {
                return ["status" => "success", 'message' => 'Not data'];
            } else {
                return $results;
            }
        }
    }

    function searchColumn(string $label, string $input)
{
    if (empty($input) || empty($label)) {
        return ["status" => "error", 'message' => 'Enter both label and input to search'];
    } else {
        $results = Restaurants::where($label, 'like', '%' . $input . '%')->get();
        
        if ($results->isEmpty()) {
            return ["status" => "success", 'message' => 'No data found'];
        } else {
            return $results;
        }
    }
}
}
