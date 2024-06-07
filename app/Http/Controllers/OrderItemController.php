<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdersItems;
use App\Models\Dishes;
use App\Models\Restaurants;
use App\Models\User;
use App\Models\Orders;
use Illuminate\Support\Facades\Schema;

class OrderItemController extends Controller
{
    function create(Request $request)
    {
        if (!$request->order_id || !$request->user_id || !$request->restaurant_id || !$request->option) {
            return response()->json(["status" => "error", "message" => "Vui lòng nhập đủ thông tin "]);
        } else {

            $list_item = Cart::where('user_id', $user_id) 
            ->where('restaurant_id', $restaurant_id)->get();

            foreach ($list_item as $item) {
                $dish = Dishes::where('id', $item->item_id)->first();

                if (!$dish) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không tìm thấy id món ăn đó ' . $index
                    ]);
                }
            
                $item = new OrdersItems;
                $item->order_id = $request->input("order_id");
                $item->item_id  = $dish->id;
                $item->quantity = $item["quantity"];
                $item->options = $request->option;
                $item->save();            
            }

            return response()->json(["status" => "success", "message" => "Đặt hàng thành công"]);
        }

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
    }

    function getAll(string $order_id)
    {
        $list_item = OrdersItems::where('order_id', $order_id)->get();

        $list = [];

        foreach ($list_item as $item) {
                array_push($list, [
                    'order_id' => $item->order_id,
            'item_id' => $item->item_id,
            'quantity' => $item->quantity,
            'options' => $item->options
                ]);
            }

        return response()->json($list);

    }

    function getAllAll()
    {
        $list_item = Orders::query()->get();

        $list = [];
       

        foreach ($list_item as $item) {
            $res = Restaurants::where('id', $item->restaurant_id)->first();
            $user = User::where('id', $item->user_id)->first();
            $orders = OrdersItems::where('order_id', $item->id)->get();
            
            $details = [];
            $money = [];

            foreach ($orders as $order) {
                $dish = Dishes::where('id', $order->item_id)->first();
                array_push($details, [
                    'name' => $dish->name,
                    'price' => $dish->price,
                    'quantity' => $order->quantity,
                ]);
            }

            array_push($money, [
                'price' => $item->price,
                'ship' => $item->ship,
                'discount' => $item->discount,
                'total_amount' => $item->total_amount,
            ]);  

            array_push($list, [
                'restaurant_name' => $res->name,
                'name' => $user->name,
                'phone' => $user->phone,
                'address' => $user->address,
                'money' => $money,
                'option' => $orders[0]->options,
                'dishes' => $details,
                'created_at' => $item->created_at,
            ]);  
        }
        return $list;

    }
    
    function getAllByRes(string $user_id)
    {
        $id = Restaurants::where('user_id', $user_id)->first()->id;
      
        $list_item = Orders::where('restaurant_id', $id)->get();
    
        $list = [];
        $details = [];
        $money = [];
        foreach ($list_item as $item) {
            $user = User::where('id', $item->user_id)->first();
            $orders = OrdersItems::where('order_id', $item->id)->get();
            
            foreach ($orders as $order) {
                $dish = Dishes::where('id', $order->item_id)->first();
                array_push($details, [
                    'name' => $dish->name,
                    'price' => $dish->price,
                    'quantity' => $order->quantity,
                ]);
            }

            array_push($money, [
                'price' => $item->price,
                'ship' => $item->ship,
                'discount' => $item->discount,
                'total_amount' => $item->total_amount,
            ]);  

            array_push($list, [
                'name' => $user->name,
                'phone' => $user->phone,
                'address' => $user->address,
                'money' => $money,
                'option' => $orders[0]->options,
                'dishes' => $details,
                'created_at' => $item->created_at,
            ]);  
        }
        return $list;
                 
    }

    function getItem(string $order_id, string $item_id)
    {
        try {
            $item = OrdersItems::where('order_id', $order_id)
            ->where('item_id', $item_id)->firstOrFail();

            return response()->json($item);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }


    function update(Request $request)
    {
        try {
                $item = OrdersItems::where('order_id', $request->input("order_id"))
                    ->where('item_id', $request->input("old_item_id")) ->firstOrFail();

                $dish_id = $request->input("item_id");
                $dish = Dishes::find($dish_id);

                if (!$dish) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không tìm thấy id món ăn đó ' . $index
                    ]);
                } 

            if ($request->item_id) {
                $item->item_id = $request->input("item_id");
            }
            if ($request->quantity) {
                $item->quantity = $request->input("quantity");
            }
            if ($request->options) {
                $item->options  = $request->input("options");
            }
            $item->update();

            return response()->json(['status' => "SUCCESS", "data" => $item]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => "error", 'message' => 'ID không tồn tại']);
        }

    }

    function delete(string $order_id, string $item_id)
    {
        $item = OrdersItems::where('order_id', $order_id)
                    ->where('item_id', $item_id)
                    ->firstOrFail();
        if (!$item) {
            return response()->json(["status" => "error", "message" => "ID không tồn tại"]);
        } else {
            $item->delete();
            return response()->json(["status" => "success", "message" => "Xoá thành công"]);
        }
    }
    function deleteAll(string $order_id)
    {
        $item = OrdersItems::where('order_id', $order_id);
        if (!$item) {
            return response()->json(["status" => "error", "message" => "ID không tồn tại"]);
        } else {
            $user->delete();
            return response()->json(["status" => "success", "message" => "Xoá thành công"]);
        }
    }

    function search(string $input)
    {
        if (empty($input)) {
            return ["status" => "error", 'message' => 'Vui lòng nhập từ khoá tìm kiếm'];
        } else {
            if (empty($input)) {
                return ["status" => "error", 'message' => 'Vui lòng nhập từ khoá tìm kiếm'];
            } else {
                $results = OrdersItems::where(function ($query) use ($input) {
                    $columns = Schema::getColumnListing('orderitems');
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'like', '%' . $input . '%');
                    }
                })->get();
            
                if ($results->isEmpty()) {
                    return ["status" => "success", 'message' => 'Không tìm thấy kết quả'];
                } else {
                    return $results;
                }
            }
        }
    }

  
    
}
