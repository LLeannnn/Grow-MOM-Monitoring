<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ibu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nama_ibu');
            $table->string('nik', 16)->unique();
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('no_telepon', 20);
            $table->enum('pekerjaan', ['ibu_rumah_tangga', 'pns', 'swasta', 'wiraswasta', 'petani', 'lainnya'])->default('ibu_rumah_tangga');
            $table->enum('pendidikan', ['sd', 'smp', 'sma', 'd3', 's1', 's2', 's3'])->default('sma');
            $table->enum('status_pernikahan', ['menikah', 'belum_menikah', 'cerai'])->default('menikah');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ibu');
    }
};
