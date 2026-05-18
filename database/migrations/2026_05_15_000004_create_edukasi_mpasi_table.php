<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('edukasi_mpasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->enum('kategori_usia', ['6_bulan', '7_9_bulan', '10_12_bulan', '12_24_bulan', 'umum']);
            $table->longText('konten');
            $table->string('gambar')->nullable();
            $table->string('tags')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edukasi_mpasi');
    }
};
