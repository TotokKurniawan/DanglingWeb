<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relations
    public function seller()
    {
        return $this->hasOne(Seller::class, 'user_id');
    }

    public function buyer()
    {
        return $this->hasOne(Buyer::class, 'user_id');
    }

    /**
     * Determine default guard name for Spatie Permission.
     * Use 'api' for API routes, otherwise 'web'.
     */
    protected function getDefaultGuardName(): string
    {
        $request = request();
        if ($request && $request->is('api/*')) {
            return 'api';
        }

        return 'web';
    }
}
