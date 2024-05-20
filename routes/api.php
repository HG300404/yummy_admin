<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ReviewController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//User
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::get('user/getAll', [UserController::class, 'getAll']);
Route::get('user/getUser/{id}', [UserController::class, 'getUser']);
Route::put('user/update', [UserController::class, 'update']);
Route::delete('user/delete/{id}', [UserController::class, 'delete']);
Route::get('user/search/{input}', [UserController::class, 'search']);


//Restaurant
Route::post('restaurant/create', [RestaurantController::class, 'create']);
Route::get('restaurant/getAll', [RestaurantController::class, 'getAll']);
Route::get('restaurant/getItem/{id}', [RestaurantController::class, 'getItem']);
Route::put('restaurant/update', [RestaurantController::class, 'update']);
Route::delete('restaurant/delete/{id}', [RestaurantController::class, 'delete']);
Route::get('restaurant/search/{input}', [RestaurantController::class, 'search']);


//Dish
Route::post('dish/create', [DishController::class, 'create']);
Route::get('dish/getAll', [DishController::class, 'getAll']);
Route::get('dish/getRecent', [DishController::class, 'getRecent']);
Route::get('dish/getItem/{id}', [DishController::class, 'getItem']);
Route::put('dish/update', [DishController::class, 'update']);
Route::delete('dish/delete/{id}', [DishController::class, 'delete']);
Route::get('dish/search/{input}', [DishController::class, 'search']);


//Order
Route::post('order/create', [OrderController::class, 'create']);
Route::get('order/getAll', [OrderController::class, 'getAll']);
Route::get('order/getItem/{id}', [OrderController::class, 'getItem']);
Route::get('order/getTopRestaurant', [OrderController::class, 'getTopRestaurant']);
Route::put('order/update', [OrderController::class, 'update']);
//Route::post('getOrderByUserDate/{user_id}/{date}', [OrderController::class, 'getOrderByUserDate']);
Route::delete('order/delete/{id}', [OrderController::class, 'delete']);
//Route::delete('dish/delete/{id}', [OrderController::class, 'deleteAll']);
Route::get('order/search/{input}', [OrderController::class, 'search']);
//Route::get('revenue', [OrderController::class, 'revenue']);


//OrderItems
Route::post('orderItems/create', [OrderItemController::class, 'create']);
Route::get('orderItems/getAll/{order_id}', [OrderItemController::class, 'getAll']);
Route::get('orderItems/getItem/{order_id}/{item_id}', [OrderItemController::class, 'getItem']);
Route::put('orderItems/update', [OrderItemController::class, 'update']);
//Route::post('getOrderByUserDate/{user_id}/{date}', [OrderController::class, 'getOrderByUserDate']);
Route::delete('orderItems/delete/{order_id}/{item_id}', [OrderItemController::class, 'delete']);
Route::delete('orderItems/deleteAll/{order_id}', [OrderItemController::class, 'deleteAll']);
Route::get('orderItems/search/{input}', [OrderItemController::class, 'search']);
//Route::get('revenue', [OrderController::class, 'revenue']);


//Comment
Route::post('comment/create', [ReviewController::class, 'create']);
Route::get('comment/getItemByRate/{rate}', [ReviewController::class, 'getItemByRate']);
Route::get('comment/getItemByDish/{item_id}', [ReviewController::class, 'getItemByDish']);
Route::get('comment/getItemByRestaurant/{restaurant_id}', [ReviewController::class, 'getItemByRestaurant']);
Route::delete('comment/delete/{item_id}/{user_id}', [ReviewController::class, 'delete']);
Route::delete('comment/deleteAll', [ReviewController::class, 'deleteAll']);
Route::get('comment/search/{input}', [ReviewController::class, 'search']);


// //Cart
// Route::post('getQuantity', [CartController::class, 'getQuantity']);
// Route::post('addCart', [CartController::class, 'addCart']);
// Route::post('getItemCart', [CartController::class, 'getItemCart']);
// Route::delete('removeItemCart', [CartController::class, 'removeItemCart']);
// Route::delete('removeAllCart', [CartController::class, 'removeAllCart']);
// Route::post('changeAmount', [CartController::class, 'changeAmount']);


