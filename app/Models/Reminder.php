<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $table = 'reminders';

    protected $fillable = [
        'ibu_id', 'anak_id', 'judul', 'pesan', 'tanggal_reminder',
        'tipe', 'status', 'kirim_sms', 'no_telepon',
    ];

    protected $casts = [
        'tanggal_reminder' => 'datetime',
        'kirim_sms' => 'boolean',
    ];

    public function ibu(): BelongsTo
    {
        return $this->belongsTo(Ibu::class, 'ibu_id');
    }

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class, 'anak_id');
    }

    public function getTipeLabelAttribute(): array
    {
        return match($this->tipe) {
            'imunisasi' => ['label' => 'Imunisasi', 'icon' => '💉', 'class' => 'tipe-imunisasi'],
            'posyandu'  => ['label' => 'Posyandu',  'icon' => '🏥', 'class' => 'tipe-posyandu'],
            'mpasi'     => ['label' => 'MPASI',     'icon' => '🥕', 'class' => 'tipe-mpasi'],
            'kontrol'   => ['label' => 'Kontrol',   'icon' => '👨‍⚕️', 'class' => 'tipe-kontrol'],
            default     => ['label' => 'Lainnya',   'icon' => '🔔', 'class' => 'tipe-lainnya'],
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->tanggal_reminder->isPast() && $this->status === 'aktif';
    }
}
