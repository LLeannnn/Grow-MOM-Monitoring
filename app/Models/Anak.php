<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Anak extends Model
{
    protected $table = 'anak';

    protected $fillable = [
        'ibu_id', 'nama_anak', 'tanggal_lahir', 'jenis_kelamin',
        'berat_lahir', 'panjang_lahir', 'golongan_darah', 'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'berat_lahir' => 'decimal:2',
        'panjang_lahir' => 'decimal:2',
    ];

    public function ibu(): BelongsTo
    {
        return $this->belongsTo(Ibu::class, 'ibu_id');
    }

    public function pertumbuhan(): HasMany
    {
        return $this->hasMany(Pertumbuhan::class, 'anak_id')->orderBy('tanggal_pengukuran', 'desc');
    }

    public function recallGizi(): HasMany
    {
        return $this->hasMany(RecallGizi::class, 'anak_id')->orderBy('tanggal', 'desc');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class, 'anak_id');
    }

    public function feedbackAnak(): HasMany
    {
        return $this->hasMany(FeedbackAnak::class, 'anak_id')->orderBy('created_at', 'desc');
    }

    public function getUmurBulanAttribute(): int
    {
        return (int) $this->tanggal_lahir->diffInMonths(now());
    }

    public function getUmurLabelAttribute(): string
    {
        $bulan = $this->umur_bulan;
        if ($bulan < 12) return "{$bulan} bulan";
        $tahun = intdiv($bulan, 12);
        $sisaBulan = $bulan % 12;
        return $sisaBulan > 0 ? "{$tahun} tahun {$sisaBulan} bulan" : "{$tahun} tahun";
    }

    public function getPertumbuhanTerakhirAttribute(): ?Pertumbuhan
    {
        return $this->pertumbuhan()->latest('tanggal_pengukuran')->first();
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}
