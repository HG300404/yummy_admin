<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdersItems;
use App\Models\Dishes;
use App\Models\Restaurants;
use App\Models\User;
use App\Models\Orders;
use App\Models\Cart;
use Illuminate\Support\Facades\Schema;

class OrderItemController extends Controller
{
    function create(Request $request)
    {
        if (!$request->order_id || !$request->user_id || !$request->restaurant_id) {
            return response()->json(["status" => "error", "message" => "Enter full information"]);
        } else {

            $list_item = Cart::where('user_id', $request->user_id) 
            ->where('restaurant_id', $request->restaurant_id)->get();
          
            foreach ($list_item as $item1) {
                $dish = Dishes::where('id', $item1->item_id)->first();
          
                if (!$dish) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không tìm thấy id món ăn đó ' . $index
                    ]);
                }
            
                $item = new OrdersItems;
                $item->order_id = $request->input("order_id");
                $item->item_id  = $dish->id;
                $item->quantity = $item1->quantity;
                $item->options = $request->option;
                $item->save();  
                
                $item1->delete();
            }
            

            return response()->json(["status" => "success", "message" => "Đặt hàng thành công"]);
        }  
    }

    function getAll(string $order_id)
    {
        $list_item = OrdersItems::where('order_id', $order_id)->get();

        $list = [];
        $dishes = [];
        $count = 0;

        foreach ($list_item as $item) {
            $dish = Dishes::where('id', $item->item_id)->first();
           
            if ($dish) {
                array_push($dishes, [
                    'img' =>$dish->img,
                    'dish_name' => $dish->name,
                    'quantity' => $item->quantity,
                ]);
                $count += $item->quantity;
            }
        }
        array_push($list, [
            'order_id' => $order_id,
            'dishes' => $dishes,
            'length' => $count,
            'options' => $list_item[0]->options
        ]);

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
        $item = OrdersItems::where('order_id', $order_id)->get();
        $order = Orders::where('id',$order_id)->first();
        if (!$order || !$item) {
            return response()->json(["status" => "error", "message" => "ID không tồn tại"]);
        } else {
            $order->delete();
            $item->delete();
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
