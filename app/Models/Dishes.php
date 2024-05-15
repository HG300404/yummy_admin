<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dishes extends Model
{
    use HasFactory;
    protected $table = "dishes";
    protected $primaryKey = "id";
    protected $fillable = [
        'restaurant_id',
        'name',
        'img',
        'price',
        'rate',
        'type'
    ];
}
