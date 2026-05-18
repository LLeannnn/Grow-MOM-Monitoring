<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /** Apakah user ini admin? */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Profil ibu yang terhubung dengan akun ini */
    public function ibu(): HasOne
    {
        return $this->hasOne(Ibu::class, 'user_id');
    }
}
