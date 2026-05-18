<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ibu_id')->constrained('ibu')->onDelete('cascade');
            $table->foreignId('anak_id')->constrained('anak')->onDelete('cascade');
            $table->string('judul');
            $table->text('pesan');
            $table->dateTime('tanggal_reminder');
            $table->enum('tipe', ['imunisasi', 'posyandu', 'mpasi', 'kontrol', 'lainnya'])->default('lainnya');
            $table->enum('status', ['aktif', 'selesai'])->default('aktif');
            $table->boolean('kirim_sms')->default(false);
            $table->string('no_telepon', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
