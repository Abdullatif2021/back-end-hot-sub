<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;  
use Spatie\Permission\Traits\HasRoles; // Include this


class Admin extends Authenticatable

{
    use HasApiTokens,HasRoles ; 
    protected $hidden = [
        'password', 'roles' // Add any other fields you want to hide
    ];
    protected $fillable = ['name', 'email', 'phone_number', 'password'];
    public function buildings() {
        return $this->hasMany(Building::class);
    }
    public function roles() {
        return $this->morphToMany(Role::class, 'roleable', 'role_user', 'roleable_id', 'role_id');
    }
}
