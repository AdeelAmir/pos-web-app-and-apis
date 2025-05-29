<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Demand extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function demandDetails()
    {
        return $this->hasMany(DemandDetail::class, 'demand_id', 'id');
    }
}
