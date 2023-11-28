<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPassword as ResetPasswordNotification;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function sendPasswordResetNotification($token)
    {
        // Your your own implementation.
        $this->notify(new ResetPasswordNotification($token));
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'age',
        'address',
        'password',
        'user_type',
        'open_close_time',
        'open_close_date',
        'latitude',
        'longitude',
        'filename',
        'path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //a user may has many appointments
    public function appointments(){
        return $this->hasMany(Appointment::class, 'user_id');
    }

    //each user id refer to one doctor id
    public function shop(){
        return $this->hasOne(Shops::class, 'shop_id');
    }

    //same go to user details
    public function user_details(){
        return $this->hasOne(UserDetails::class, 'user_id');
    }


    //a user may has many reviews
    public function reviews(){
        return $this->hasMany(Reviews::class, 'user_id');
    }

    //this will get all shops with their respective reviews
    public function reviews_for_shop()
    {
        return $this->hasMany(Reviews::class, 'shop_id');
    }
}
