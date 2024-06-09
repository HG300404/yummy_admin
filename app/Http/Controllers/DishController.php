<?php

namespace App\Http\Controllers;
use App\Models\Restaurants;
use Illuminate\Http\Request;
use App\Models\Dishes;
use App\Models\OrdersItems;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
class DishController extends Controller
{
    function create(Request $request)
    {
        if (!$request->restaurant_id || !$request->name || !$request->price || !$request->type  || !$request->img) {
            return response()->json(["status" => "error", "message" => "Vui lòng nhập đủ thông tin"]);
        } else {

            $item = new Dishes;
            $item->restaurant_id = $request->input("restaurant_id");
            $item->name = $request->input("name");
            $item->img = $request->input("img");
            $item->price = $request->input("price");
            $item->rate = 0;
            $item->type = $request->input('type');
            $item->save();            
            return response()->json(["status" => "success", "message" => "Thêm món mới thành công"]);
        }
    }

    function getAll(int $user_id)
    {
        try {
            $res = Restaurants::where('user_id',$user_id)->first();
            $list_item = Dishes::where('restaurant_id',$res->id)->
            orderBy('rate', 'desc')->get();
            // if (!$$list_item) {
            //     return ["status" => "success", 'message' => 'Không có dữ liệu'];
            // }
            $list = [];
            foreach ($list_item as $item) {
                        array_push($list, [
                            'id' => $item->id,
                            'name' => $item->name,
                            'img' => $item->img,
                            'price' =>$item->price,
                            'rate' =>$item->rate,
                            'type' =>$item->type,
                            'created_at' => $item->created_at,
                            'updated_at' =>$item->updated_at,
                        ]);
                       }
                    
            return response()->json($list);
      
        } catch (\Exception $e) {
            return response()->json(["status" => "error", "message" => 'Error']);
        }

    }



    function getAllHome()
    {
        $topDishes = Dishes::join('orderItems', 'dishes.id', '=', 'orderItems.item_id')
    ->join('orders', 'orderItems.order_id', '=', 'orders.id')
    ->join('restaurants', 'dishes.restaurant_id', '=', 'restaurants.id')
    ->select('dishes.img', 'dishes.name', 'restaurants.name AS restaurant_name', 'dishes.type', 'dishes.rate', 'dishes.price')
    ->groupBy('dishes.id', 'dishes.name', 'restaurants.name', 'dishes.type', 'dishes.rate', 'dishes.price', 'dishes.img')
    ->orderByRaw('COUNT(orderItems.item_id) DESC')
    ->get();

// Trả về danh sách kết quả
return $topDishes;
    }


    function getRecent(Request $request)
    {
        try {
            $list_item = Dishes::orderBy('created_at', 'desc')->get();
            $list = [];
            foreach ($list_item as $item) {
                $res_name = Restaurants::where('id', $item->restaurant_id)->first();
                        array_push($list, [
                            'id' => $item->id,
                            'restaurant_name' => $res_name->name,
                            'name' => $item->name,
                            'img' => $item->img,
                            'price' =>$item->price,
                            'rate' =>$item->rate,
                            'type' =>$item->type,
                            'created_at' => $item->created_at,
                            'updated_at' =>$item->updated_at,
                        ]);
                       }
                    
            return response()->json($list);
      
        } catch (\Exception $e) {
            return response()->json(["status" => "error", "message" => 'Có lỗi xảy ra khi tải dữ liệu']);
        }

    }

    
    function getItem(string $id)
    {
        try {
            $item = Dishes::findOrFail($id);
            // $list = [];
            // $res_name = Restaurants::where('id', $item->restaurant_id)->first();
            //             array_push($list, [
            //                 'id' => $item->id,
            //                 'restaurant_name' => $res_name->name,
            //                 'name' => $item->name,
            //                 'img' => $item->img,
            //                 'price' =>$item->price,
            //                 'rate' =>$item->rate,
            //                 'type' =>$item->type,
            //                 'created_at' => $item->created_at,
            //                 'updated_at' =>$item->updated_at,
            //             ]);
            return response()->json($item);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }

    function update(Request $request)
    {
        try {
            $item = Dishes::findOrFail($request->id);

            // if ($request->restaurant_id && $request->restaurant_name) {
            //     $item1 = Restaurant::findOrFail($request->restaurant_id);
            //     $item1->name = $request->input("restaurant_name");
            //     $item1->update();
            // }
            if ($request->name) {
                $item->name = $request->input("name");
            }
            if ($request->img) {
                $item->img = $request->input("img");
            }
            if ($request->price) {
                $item->price = $request->input("price");
            }
            if ($request->rate) {
                $item->rate = $request->input("rate");
            }
            if ($request->type) {
                $item->type = $request->input("type");
            }
            $item->update();

            return response()->json(['status' => "success", "data" => $item]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => "error", 'message' => 'ID không tồn tại']);
        }

    }
    function delete(string $id)
    {
        $item = Dishes::where('id', $id)->first();
        if (!$item) {
            return response()->json(["status" => "error", "message" => "ID không tồn tại"]);
        } else {
            $item->delete();
            return response()->json(["status" => "success", "message" => "Xoá thành công"]);
        }
    }

    function search(string $input, int $res_id)
    {
        if (empty($input)) {
            return ["status" => "error", 'message' => 'Vui lòng nhập từ khoá tìm kiếm'];
        } else {
            $results = Dishes::where(function ($query) use ($input) {
                $columns = Schema::getColumnListing('dishes');
                foreach ($columns as $column) {
                    $query->orWhere($column, 'like', '%' . $input . '%');
                }
            })->where('restaurant_id', $res_id) ->get();
        
            if ($results->isEmpty()) {
                return ["status" => "success", 'message' => 'Không tìm thấy kết quả'];
            } else {
                return $results;
            }
        }
    }
}
