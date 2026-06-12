<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'nomer', 'password', 'role', 'last_activity_at'];

    protected $hidden = ['password', 'remember_token'];

    // Gunakan 'nomer' sebagai username untuk Auth
    public function getAuthIdentifierName(): string
    {
        return 'nomer';
    }

    protected function casts(): array
    {
        return [
            'password'         => 'hashed',
            'last_activity_at' => 'datetime',
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

    /** Riwayat aktivitas browsing user */
    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class);
    }

    /** Apakah user sedang online? (aktif dalam 5 menit terakhir) */
    public function getIsOnlineAttribute(): bool
    {
        return $this->last_activity_at && $this->last_activity_at->gt(now()->subMinutes(5));
    }
}
