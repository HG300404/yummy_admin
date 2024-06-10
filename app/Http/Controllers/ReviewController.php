<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dishes;
use App\Models\User;
use App\Models\Reviews;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\Restaurants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    function create(Request $request)
    {
        if (!$request->order_id || !$request->user_id || !$request->comment || !$request->rating ) {
            return response()->json(["status" => "error", "message" => "Vui lòng nhập đủ thông tin "]);
        } else {
            $order = OrdersItems::where('order_id', $request->order_id)->get();
            foreach ($order as $item){
                $dish = Dishes::where('id', $item->item_id)->firstOrFail();
                $dish->rate += $request->rating;
                

                $review = new Reviews;
                $review->item_id = $item->item_id;
                $review->user_id  = $request->user_id;
                $review->rating = $request->rating;
                $review->comment = $request->comment;
    
                $review->save();       
            }
            
            return response()->json(["status" => "success", "message" => "Lưu đánh giá thành công"]);
        }
    }
    

    function getItemByRate(string $rate)
    {
        try {
            $list_item = Reviews::where('rating', $rate)->get();
            if ($list_item->count() === 0){
                return response()->json(["status" => "success", "message" => 'Không có dữ liệu']);
            } else {
                $list = [];

           foreach ($list_item as $item) {
               $dish = Dishes::where('id',$item->item_id)->firstOrFail();
               $restaurant = Restaurants::where('id', $dish->restaurant_id)->firstOrFail();
               $user = User::where('id', $item->user_id)->firstOrFail();

                array_push($list, [
                    'dish_name' => $dish->name,
                    'restaurant' => $restaurant->name,
                    'user_name' => $user->name,
                    'comment' => $item->comment
                ]);
            }

            return response()->json($list);
            }
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }


    function getItemByDish(string $item_id)
    {
        try {
            $list_item = Reviews::where('item_id', $item_id)->get();
            if ($list_item->count() === 0){
                return response()->json(["status" => "success", "message" => 'Không có dữ liệu']);
            } else {
                $list = [];

                foreach ($list_item as $item) {
                       $user = User::where("id", $item->user_id)->firstOrFail();
        
                        array_push($list, [
                            "rating" =>$item->rating,
                            'user_name' => $user->name,
                            'comment' => $item->comment
                        ]);
                    }
        
                    return response()->json($list);
            }
           
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }

    
    function getItemByRestaurant(string $user_id)
    {
        try {
            $id = Restaurants::where('user_id', $user_id)->first()->id;
            $list = [];
            $review = Reviews::where('restaurant_id', $id)->get();
            foreach ($review as $item1){
                $user = User::where('id', $item1->user_id)->firstOrFail();
                array_push($list, [
                    'rating' =>$item1->rating,
                    'user_name' => $user->name,
                    'comment' => $item1->comment,
                    'created_at' => $item1->created_at,
                ]);
             }
            return response()->json($list);
           
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(["status" => "error", "message" => 'ID không tồn tại']);
        }

    }

    function delete(string $item_id, string $user_id)
    {
        $item = Reviews::where('user_id', $user_id)
                    ->where('item_id', $item_id)
                    ->firstOrFail();
        if (!$item) {
            return response()->json(["status" => "error", "message" => "ID không tồn tại"]);
        } else {
            $item->delete();
            return response()->json(["status" => "success", "message" => "Xoá thành công"]);
        }
    }

    
    public function deleteAll()
    {
        $item = Reviews::query();
        if ($item->count() === 0) {
            return response()->json(["status" => "error", "message" => "Không có dữ liệu trong bảng"]);
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
                $results = Reviews::where(function ($query) use ($input) {
                    $columns = Schema::getColumnListing('reviews');
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
    function totalRating(Request $request)
    {
        $list = DB::table('reviews')
        ->select('rating AS name', DB::raw('count(rating) AS count'))
        ->groupBy('name')
        ->get();

    return response()->json($list);
    }


    function countRegister(Request $request)
    {
        $userCount = User::count();
        $restaurantCount = Restaurants::count();

        $response = [
            ['name' => 'Khách đăng kí', 'Se' => $userCount],
            ['name' => 'Chủ đăng kí', 'Se' => $restaurantCount],
        ];
        return response()->json($response);
    }

    //Owner
    function totalRatingByOwner(int $user_id)
    {
        $res = Restaurants::where('user_id',$user_id)->first();
        if (!$res) {
            return ["status" => "success", 'message' => 'Không tìm thấy kết quả'];
        }

        $list = DB::table('reviews')
        ->where('restaurant_id', $res->id)
        ->select('rating AS name' , DB::raw('count(name) AS count'))
        ->groupBy('name')
        ->get();

    return response()->json($list);
    }
    //t nhớ rồi, mi đang dùng db cũ
}
