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
        if (!$request->name || !$request->address || !$request->phone || !$request->opening_hours) {
            return response()->json(["status" => "error", "message" => "Vui lòng nhập đủ thông tin"]);
        } else {
            $item = new Restaurants;

            $check = Restaurants::where('name', $request->name)->first();
            if ($check) {
                return ["status" => "error", "message" => "Tên đã tồn tại"];
            } else {
                $item->name = $request->input("name");
            }
            $item->phone = $request->input("phone");
            $item->address = $request->input("address");
            $item->opening_hours = $request->input('opening_hours');
            $item->save();            
            return response()->json(["status" => "success", "message" => "Thêm quán mới thành công"]);
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
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
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
            $item->update();

            return response()->json(['status' => "SUCCESS", "data" => $item]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => "error", 'message' => 'ID không tồn tại']);
        }

    }
    function delete(string $id)
    {
        $item = Restaurants::where('id', $id)->first();
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
                $results = Restaurants::where(function ($query) use ($input) {
                    $columns = Schema::getColumnListing('restaurants');
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
