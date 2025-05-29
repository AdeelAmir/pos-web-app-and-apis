<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function getIconAttribute($value)
    {
        $imageUrl = $value != '' ? asset('public/storage/categories/' . $value) :  '';
        return $imageUrl;
    }
}
