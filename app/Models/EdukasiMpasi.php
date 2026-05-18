<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdukasiMpasi extends Model
{
    protected $table = 'edukasi_mpasi';

    protected $fillable = [
        'judul', 'kategori_usia', 'bahan_makanan', 'tekstur_makanan', 'konten', 'gambar', 'tags', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori_usia) {
            '6_bulan'    => '6 Bulan',
            '7_9_bulan'  => '7-9 Bulan',
            '10_12_bulan' => '10-12 Bulan',
            '12_24_bulan' => '12-24 Bulan',
            default      => 'Umum',
        };
    }

    public function getTagsArrayAttribute(): array
    {
        return $this->tags ? explode(',', $this->tags) : [];
    }

    public function getKontenRingkasAttribute(): string
    {
        return \Str::limit(strip_tags($this->konten), 150);
    }
}
