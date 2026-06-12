<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserActivity extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'url', 'page_title', 'method',
        'ip_address', 'user_agent', 'session_id', 'visited_at',
    ];

    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /* ── Relations ─────────────────────────────── */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ── Scopes ────────────────────────────────── */
    public function scopeToday($q)
    {
        return $q->whereDate('visited_at', today());
    }

    public function scopeThisWeek($q)
    {
        return $q->where('visited_at', '>=', now()->startOfWeek());
    }

    public function scopeThisMonth($q)
    {
        return $q->where('visited_at', '>=', now()->startOfMonth());
    }

    public function scopeByUser($q, $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopePeriod($q, string $period)
    {
        return match ($period) {
            'today'   => $q->today(),
            'week'    => $q->thisWeek(),
            'month'   => $q->thisMonth(),
            default   => $q,
        };
    }

    /* ── Route-to-Label mapping ────────────────── */
    public static function pageLabels(): array
    {
        return [
            'user.dashboard'      => 'Beranda',
            'anak.index'          => 'Data Anak',
            'anak.show'           => 'Profil Anak',
            'anak.create'         => 'Tambah Anak',
            'anak.edit'           => 'Edit Anak',
            'pertumbuhan.index'   => 'Pertumbuhan',
            'pertumbuhan.create'  => 'Input Pengukuran',
            'pertumbuhan.show'    => 'Analisis Pertumbuhan',
            'recall.index'        => 'Recall Gizi',
            'recall.create'       => 'Input Recall Gizi',
            'edukasi.index'       => 'Edukasi MPASI',
            'edukasi.show'        => 'Detail Edukasi',
            'reminder.index'      => 'Reminder',
            'feedback.index'      => 'Feedback',
            'feedback.show'       => 'Detail Feedback',
            'onboarding'          => 'Onboarding',
        ];
    }

    /**
     * Hitung estimasi durasi sesi (menit) dari kumpulan activity records.
     * Logika: hitung selisih waktu antar kunjungan berurutan.
     * Jika gap > 30 menit → dianggap sesi baru (idle/tutup browser).
     */
    public static function calculateSessionDuration($activities): int
    {
        if ($activities->count() < 2) {
            return $activities->count(); // 1 visit = ~1 menit estimasi
        }

        $sorted   = $activities->sortBy('visited_at')->values();
        $totalMin = 0;
        $maxGap   = 30; // menit — gap > 30 min = sesi baru

        for ($i = 1; $i < $sorted->count(); $i++) {
            $diff = $sorted[$i]->visited_at->diffInMinutes($sorted[$i - 1]->visited_at);
            if ($diff <= $maxGap) {
                $totalMin += $diff;
            }
        }

        return max($totalMin, 1); // minimal 1 menit
    }
}
