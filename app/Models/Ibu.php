<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ibu extends Model
{
    protected $table = 'ibu';

    protected $fillable = [
        'user_id', 'nama_ibu', 'nik', 'tanggal_lahir', 'alamat',
        'no_telepon', 'pekerjaan', 'pendidikan', 'status_pernikahan', 'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function anak(): HasMany
    {
        return $this->hasMany(Anak::class, 'ibu_id');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class, 'ibu_id');
    }

    public function getUmurAttribute(): int
    {
        return $this->tanggal_lahir->age;
    }

    public function getPendidikanLabelAttribute(): string
    {
        return match($this->pendidikan) {
            'sd' => 'SD', 'smp' => 'SMP', 'sma' => 'SMA',
            'd3' => 'D3', 's1' => 'S1', 's2' => 'S2', 's3' => 'S3',
            default => $this->pendidikan,
        };
    }

    public function getPekerjaanLabelAttribute(): string
    {
        return match($this->pekerjaan) {
            'ibu_rumah_tangga' => 'Ibu Rumah Tangga',
            'pns' => 'PNS', 'swasta' => 'Swasta',
            'wiraswasta' => 'Wiraswasta', 'petani' => 'Petani',
            default => 'Lainnya',
        };
    }
}
