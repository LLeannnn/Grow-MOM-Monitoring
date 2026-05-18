<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recall_gizi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anak_id')->constrained('anak')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('waktu_makan', ['pagi', 'siang', 'malam', 'snack']);
            $table->string('nama_makanan');
            $table->decimal('jumlah', 8, 2);
            $table->string('satuan', 50)->default('gram');
            $table->decimal('kalori', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('karbohidrat', 8, 2)->default(0);
            $table->decimal('lemak', 8, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recall_gizi');
    }
};
