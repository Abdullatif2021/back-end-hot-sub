<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Service extends Model
{
    protected $fillable = [
        'name',
        'ar_name',
        'image'
    ];
    public function request() {
        return $this->hasOne(Request::class);
    }
}
