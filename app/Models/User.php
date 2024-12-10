<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        // 'nama',
        'email',
        'password',
        'role',
        'foto', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi dengan Pedagang
    public function pedagang()
    {
        return $this->hasOne(Pedagang::class, 'user_id');
    }
    public function pembeli()
    {
        return $this->hasOne(Pembeli::class, 'user_id');
    }
}
