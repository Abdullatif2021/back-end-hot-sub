<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['index','en_name', 'fr_name'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}

