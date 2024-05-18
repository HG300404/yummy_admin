<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrdersItems;
use App\Models\Dishes;
use Illuminate\Support\Facades\Schema;

class OrderItemController extends Controller
{
    function create(Request $request)
    {
        if (!$request->order_id || !$request->dishes ) {
            return response()->json(["status" => "error", "message" => "Vui lòng nhập đủ thông tin "]);
        } else {
            foreach ($request->dishes as $index => $dishData) {
                $item_id = $dishData['item_id'];
                $dish = Dishes::find($item_id);

                if (!$dish) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Không tìm thấy id món ăn đó ' . $index
                    ]);
                }
            
                $item = new OrdersItems;
                $item->order_id = $request->input("order_id");
                $item->item_id  = $item_id;
                $item->quantity = $dishData["quantity"];
                $item->options = $dishData["options"];
                $item->save();            
            }

            return response()->json(["status" => "success", "message" => "Đặt hàng thành công"]);
        }
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
