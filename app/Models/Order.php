<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function shop()
    {
        return $this->hasOne(Shop::class, 'id', 'shop_id');
    }

    public function partialPaymentHistory()
    {
        return $this->hasMany(PartialPayment::class, 'order_id', 'id');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function seller()
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
}
