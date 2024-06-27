<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
     protected $fillable = [
        'request_date',
        'available_time',
        'available_start_time', 
        'available_end_time',   
        'status',
        'user_id',
        'service_id',
        'description',
        'image',    
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function service() {
        return $this->belongsTo(Service::class);
    }
}
