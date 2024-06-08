<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Dishes;
use App\Models\Restaurants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    function create(Request $request)
    {
        if (!$request->user_id || !$request->item_id || !$request->restaurant_id || !$request->quantity) {
            return response()->json(["status" => "error", "message" => "Enter full infor"]);
        } else {
            $item = new Cart;
            $item->user_id = $request->input("user_id");
            $item->item_id = $request->input("item_id");
            $item->restaurant_id = $request->input("restaurant_id");
            $item->quantity = $request->input("quantity");
            $item->save();            
            return response()->json(["status" => "success", "message" => "Add new item in cart success"]);
        }
    }

    function getAll(String $user_id, String $restaurant_id)
    {
        try {
            // $list_item = Cart::orderBy('created_at', 'desc')->get();
            $list_item = Cart::where('user_id', $user_id) 
            ->where('restaurant_id', $restaurant_id) 
            ->orderBy('created_at', 'desc')->get();
            $list = [];
            // $total = 0;
            // $count = 0;
            foreach ($list_item as $item) {
                $dish = Dishes::where('id', $item->item_id)->first();
                        array_push($list, [
                            'dish_id' => $dish->id,
                            'dish_name' => $dish->name,
                            'dish_price' => $dish->price,
                            'dish_img' => $dish->img,
                            'quantity' =>$item->quantity,
                        ]);
                        // $total += $dish->price;
                        // $count += $item->quantity;
                       }
                    //    $list['total'] = $total;
                    //    $list['count'] = $count;       
            return response()->json($list);      
        } catch (\Exception $e) {
            return response()->json(["status" => "error", "message" => 'Error']);
        }
    }

    function getAllByUser(int $user_id)
    {
        try {
            $list_item = Cart::select('restaurant_id', DB::raw('SUM(quantity) as total'), DB::raw('GROUP_CONCAT(item_id) as items'))
                ->where('user_id', $user_id)
                ->groupBy('restaurant_id')
                ->get();
      
            $list = [];
            
            foreach ($list_item as $item){
                $res = Restaurants::where('id', $item->restaurant_id)->first();
                $items = explode(',', $item->items);
                $dishes = Dishes::whereIn('id', $items)->get();
                
                array_push($list, [
                    'img' => $dishes[0]->img,
                    'id' => $res->id,
                    'restaurant_name' => $res->name,
                    'address' => $res->address,
                    'count' => $item->total,
                ]);
            }
            return response()->json($list);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }


    function update(Request $request)
    {
        try {
            $item = Cart::where('user_id', $request->user_id)->
            where('restaurant_id', $request->restaurant_id)->
            where('item_id', $request->item_id)->first();

            if ($request->quantity) {
                $item->quantity = $request->input("quantity");
            }
            $item->update();

            return response()->json(['status' => "SUCCESS", "data" => $item]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => "error", 'message' => 'ID not exist']);
        }

    }
    function delete(int $user_id, int $restaurant_id, int $item_id)
    {
        $item = Cart::where('user_id', $user_id)->
            where('restaurant_id', $restaurant_id)->
            where('item_id', $item_id)->first();

        if (!$item) {
            return response()->json(["status" => "error", "message" => "ID not exist"]);
        } else {
            $item->delete();
            return response()->json(["status" => "success", "message" => "Delete success"]);
        }
    }

}
