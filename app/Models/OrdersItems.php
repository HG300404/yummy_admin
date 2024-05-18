<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdersItems extends Model
{
    use HasFactory;
    protected $table = "orderItems";
    protected $primaryKey = ['order_id', 'item_id'];
    public $incrementing = false;
    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'options'
    ];

    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $key) {
            if (isset($this->$key)) {
                $query->where($key, '=', $this->$key);
            } else {
                throw new Exception(__METHOD__ . 'Missing part of the primary key: ' . $key);
            }
        }
        return $query;
    }
}
