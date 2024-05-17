<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dishes;
use Illuminate\Support\Facades\Schema;

class OrderItemController extends Controller
{
    function create(Request $request)
    {
        if (!$request->dish_id || !$request->quantity || !$request->price || !$request->ship) {
            return response()->json(["status" => "error", "message" => "Vui lòng nhập đủ thông tin 1"]);
        } else {
            $item = new Orders;
            $item->user_id = $request->input("user_id");
            $item->restaurant_id = $request->input("restaurant_id");
            $item->price = $request->input("price");
            $item->ship = $request->input('ship');
            $item->discount = $request->input("discount");
            $item->total_amount = $request->input('total_amount');
            $item->save();            
            return response()->json(["status" => "success", "message" => "Đặt hàng thành công"]);
        }
    }

    function getAll(Request $request)
    {
        $list = Orders::query();
        $list = $list->get();
        return response()->json($list);

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
}
