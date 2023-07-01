<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'verified',
        'disaster_id',
        'name',
        'age',
        'contact_number',
        'email',
        'donation_type',
        'donation_info'
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
