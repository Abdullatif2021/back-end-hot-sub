<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Security extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $hidden = [
        'password', 'roles' // Add any other fields you want to hide
    ];
    protected $fillable = ['name', 'email', 'password', 'building_id'];

    public function building()
    {
        return $this->belongsTo(Building::class);
    }
    public function roles() {
        return $this->morphToMany(Role::class, 'roleable', 'role_user', 'roleable_id', 'role_id');
    }

}
