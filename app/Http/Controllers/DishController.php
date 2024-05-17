<?php

namespace App\Http\Controllers;
use App\Models\Restaurants;
use Illuminate\Http\Request;
use App\Models\Dishes;
use Illuminate\Support\Facades\Schema;

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

    function getAll(Request $request)
    {
        $list = Dishes::query();
        $list = $list->get();
        return response()->json($list);

    }

    function getItem(string $id)
    {
        try {
            $item = Dishes::findOrFail($id);
            return response()->json($item);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }

    function update(Request $request)
    {
        try {
            $item = Dishes::findOrFail($request->id);

            if ($request->restaurant_id) {
                $item->restaurant_id = $request->input("restaurant_id");
            }
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

            return response()->json(['status' => "SUCCESS", "data" => $item]);

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
            $user->delete();
            return response()->json(["status" => "success", "message" => "Xoá thành công"]);
        }
    }

    function search(string $input)
    {
        if (empty($input)) {
            return ["status" => "error", 'message' => 'Vui lòng nhập từ khoá tìm kiếm'];
        } else {
            $results = Dishes::where(function ($query) use ($input) {
                $columns = Schema::getColumnListing('dishes');
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
