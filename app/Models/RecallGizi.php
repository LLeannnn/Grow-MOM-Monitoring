<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecallGizi extends Model
{
    protected $table = 'recall_gizi';

    protected $fillable = [
        'anak_id', 'tanggal', 'waktu_makan', 'nama_makanan',
        'jumlah', 'satuan', 'kalori', 'protein', 'karbohidrat', 'lemak', 'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
        'kalori' => 'decimal:2',
        'protein' => 'decimal:2',
        'karbohidrat' => 'decimal:2',
        'lemak' => 'decimal:2',
    ];

    public function anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class, 'anak_id');
    }

    public function getWaktuMakanLabelAttribute(): string
    {
        return match($this->waktu_makan) {
            'pagi'  => '🌅 Pagi',
            'siang' => '☀️ Siang',
            'malam' => '🌙 Malam',
            'snack' => '🍎 Snack',
            default => $this->waktu_makan,
        };
    }
}
