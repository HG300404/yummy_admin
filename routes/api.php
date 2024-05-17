<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\DishController;
use App\Http\Controllers\OrderController;

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
Route::get('dish/getItem/{id}', [DishController::class, 'getItem']);
Route::put('dish/update', [DishController::class, 'update']);
Route::delete('dish/delete/{id}', [DishController::class, 'delete']);
Route::get('dish/search/{input}', [DishController::class, 'search']);

//Order
Route::post('order/create', [OrderController::class, 'create']);
Route::get('order/getAll', [OrderController::class, 'getAll']);
Route::get('order/getItem/{id}', [OrderController::class, 'getItem']);
Route::put('order/update', [OrderController::class, 'update']);
//Route::post('getOrderByUserDate/{user_id}/{date}', [OrderController::class, 'getOrderByUserDate']);
Route::delete('order/delete/{id}', [OrderController::class, 'delete']);
//Route::delete('dish/delete/{id}', [OrderController::class, 'deleteAll']);
Route::get('order/search/{input}', [OrderController::class, 'search']);
//Route::get('revenue', [OrderController::class, 'revenue']);

//Cart
Route::post('getQuantity', [CartController::class, 'getQuantity']);
Route::post('addCart', [CartController::class, 'addCart']);
Route::post('getItemCart', [CartController::class, 'getItemCart']);
Route::delete('removeItemCart', [CartController::class, 'removeItemCart']);
Route::delete('removeAllCart', [CartController::class, 'removeAllCart']);
Route::post('changeAmount', [CartController::class, 'changeAmount']);


