<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'price',
        'rating',
        'review',
        'reviewed_at',
    ];

    protected $dates = ['reviewed_at'];

    //public $timestamps = false;

    /*--------------------关联关系-------------------------*/
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id', 'id');
    }

    public function productSku()
    {
        return $this->belongsTo('App\Models\ProductSku', 'product_sku_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }
}
