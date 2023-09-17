<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'date',
        'day',
        'time',
        'status',
        'shop_latitude',
        'shop_longitude',
        'name',
        'contact_number',
        'email',
        'address',
        'type',
        'service'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
