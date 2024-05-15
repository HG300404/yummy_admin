<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersItems extends Model
{
    use HasFactory;
    protected $table = "orderItems";
    protected $primaryKey = "id";
    protected $fillable = [
        'item_id',
        'quantity',
        'options'
    ];
}
