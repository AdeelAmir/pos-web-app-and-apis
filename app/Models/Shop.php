<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    protected $hidden = [
        'updated_at', 'deleted_at',
    ];
}
