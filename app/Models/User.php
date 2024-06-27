<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory,HasRoles, Notifiable;
    protected $hidden = [
        'password', 'roles' // Add any other fields you want to hide
    ];
    protected $fillable = [
        'name',
        'number',
        'email',
        'fcm',
        'gender',
        'apartment_number',
        'password',
        'building_id',
    ];

    public function building() {
        return $this->belongsTo(Building::class);
    }
    public function roles() {
        return $this->morphToMany(Role::class, 'roleable', 'role_user', 'roleable_id', 'role_id');
    }
    public function requests() {
        return $this->hasMany(Request::class);
    }
}