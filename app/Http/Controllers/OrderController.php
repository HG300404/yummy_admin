<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurants;
use App\Models\OrdersItems;
use App\Models\User;
use App\Models\Orders;
use App\Models\Dishes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class OrderController extends Controller

{
    function create(Request $request)
    {
        try{
            if (!$request->user_id || !$request->restaurant_id || !$request->price || !$request->ship) {
                return response()->json(["status" => "error", "message" => "Vui lòng nhập đủ thông tin "]);
            } else {
                $item = new Orders;
                $item->user_id = $request->input("user_id");
                $item->restaurant_id = $request->input("restaurant_id");
                $item->price = $request->input("price");
                $item->ship = $request->input('ship');
                $item->discount = $request->input("discount");
                $item->total_amount = $request->input('total_amount');
                if ($request->payment == 0){
                    $item->payment = "Tiền mặt";
                } else {
                    $item->payment = "Trực tuyến";
                }
                $item->save();            
                return response()->json($item);
            }
        } catch (e){
          
        }
    }

    function getAll(Request $request)
    {
        $list = Orders::query();
        $list = $list->get();
        return response()->json($list);

    }


    function getTopRestaurant(Request $request)
    {        
        try {
            $list_item = Orders::select('restaurant_id', DB::raw('count(*) as total'))
            ->groupBy('restaurant_id')
            ->orderBy('total', 'desc')
            ->get();

            $list = [];
            foreach ($list_item as $item) {
                $res_name = Restaurants::where('id', $item->restaurant_id)->first();
                        array_push($list, [
                            'id' => $res_name->id,
                            'restaurant_name' => $res_name->name,
                            'address' => $res_name->address,
                            'open' => $res_name->opening_hours,
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
            $item = Orders::findOrFail($id);
            return response()->json($item);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }

    function getItems(int $user_id)
    {
        try {
            $list_item = Orders::where('user_id', $user_id)->get();

            $list = [];
            $count = 0;
            foreach ($list_item as $item){
                $res = Restaurants::where('id', $item->restaurant_id)->first();
                $orders = OrdersItems::where('order_id', $item->id)->get();
                $count += $orders->sum('quantity');

                $dish = Dishes::where('id', $orders[0]->item_id)->first();
                
                array_push($list, [
                    'img' => $dish->img,
                    'id' => $res->id,
                    'restaurant_name' => $res->name,
                    'address' => $res->address,
                    'count' => $count,
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
            $item = Orders::findOrFail($request->id);

            if ($request->restaurant_id) {
                $item->restaurant_id = $request->input("restaurant_id");
            }
            if ($request->user_id) {
                $item->user_id = $request->input("user_id");
            }
            if ($request->price ) {
                $item->price = $request->input("price");
            }
            if ($request->ship) {
                $item->ship = $request->input("ship");
            }
            // if ($request->discount) {
                $item->discount = $request->input("discount");
            // }
            // if ($request->total_amount) {
                $item->total_amount = $request->input("total_amount");
            // }
            if ($request->payment) {
                $item->payment = $request->input("payment");
            }
            $item->update();

            return response()->json(['status' => "SUCCESS", "data" => $item]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => "error", 'message' => 'ID không tồn tại']);
        }

    }
    function delete(string $id)
    {
        $item = Orders::where('id', $id)->first();
        if (!$item) {
            return response()->json(["status" => "error", "message" => "ID không tồn tại"]);
        } else {
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
                $results = Orders::where(function ($query) use ($input) {
                    $columns = Schema::getColumnListing('orders');
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

    //Admin
    function calculateTotalAmountByMonth(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $list = DB::table('orders')
        ->whereYear('created_at', $currentYear)
        ->select(DB::raw('MONTH(created_at) AS month, SUM(total_amount) AS total'))
        ->groupBy('month')
        ->get();

        return response()->json($list);
    }

    function calculateTotalOrderByWeekday()
    {
        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $totals = DB::table('orders')
        ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
        ->select(DB::raw('DAYOFWEEK(created_at) AS weekday, SUM(total_amount) AS total'))
        ->groupBy('weekday')
        ->get();

    return response()->json($totals);
    }

    //Owner
    function totalAmountByMonth(int $user_id)
    {

        $res = Restaurants::where('user_id',$user_id)->first();
        if (!$res) {
            return ["status" => "success", 'message' => 'Không tìm thấy kết quả'];
        }

        $currentYear = Carbon::now()->year;
        $list = DB::table('orders')
        ->where('restaurant_id', $res->id)
        ->whereYear('created_at', $currentYear)
        ->select(DB::raw('MONTH(created_at) AS month, SUM(total_amount) AS total'))
        ->groupBy('month')
        ->get();

        return response()->json($list);
    }

    function totalOrderByWeekday(int $user_id)
    {
        $res = Restaurants::where('user_id',$user_id)->first();
        if (!$res) {
            return ["status" => "success", 'message' => 'Không tìm thấy kết quả'];
        }

        $currentWeekStart = Carbon::now()->startOfWeek();
        $currentWeekEnd = Carbon::now()->endOfWeek();

        $totals = DB::table('orders')
        ->where('restaurant_id', $res->id)
        ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
        ->select(DB::raw('DAYOFWEEK(created_at) AS weekday, SUM(total_amount) AS total'))
        ->groupBy('weekday')
        ->get();

        return response()->json($totals);
    }

}
