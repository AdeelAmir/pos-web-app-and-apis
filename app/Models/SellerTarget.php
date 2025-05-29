<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerTarget extends Model
{
    use HasFactory;

    public function seller()
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    public function targetDetails()
    {
        return $this->hasMany(SellerTargetDetails::class, 'target_id', 'id');
    }
}
