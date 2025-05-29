<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public function getProfileImageAttribute($value)
    {
        $imageUrl = $value != '' ? asset('public/storage/users/' . $value) :  '';
        return $imageUrl;
    }

    // public function listings()
    // {
    //     return $this->hasMany(Listing::class, 'user_id', 'id');
    // }

    // public function package()
    // {
    //     return $this->hasOne(Package::class, 'id', 'package_id');
    // }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'device_token',
        'package_id',
        'business_logo',
        'business_name',
        'business_address',
        'business_province',
        'business_city',
        'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
