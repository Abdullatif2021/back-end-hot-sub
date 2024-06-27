<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = ['name', 'building_number', 'number_of_apartments', 'number_of_floors', 'admin_id'];
    public function admin() {
        return $this->belongsTo(Admin::class);
    }
    public function users() {
        return $this->hasMany(User::class);
    }
}
