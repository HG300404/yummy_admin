<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurants extends Model
{
    use HasFactory;
    protected $table = "restaurants";
    protected $primaryKey = "id";
    protected $fillable = [
        'name',
        'address',
        'phone',
        'opening_hours'
    ];

}
