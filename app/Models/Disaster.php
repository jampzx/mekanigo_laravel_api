<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'active',
        'title',
        'date',
        'disasterType',
        'location',
        'information',
        'filename',
        'path',
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
