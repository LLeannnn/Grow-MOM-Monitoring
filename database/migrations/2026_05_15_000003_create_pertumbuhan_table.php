<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertumbuhan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anak_id')->constrained('anak')->onDelete('cascade');
            $table->date('tanggal_pengukuran');
            $table->decimal('berat_badan', 5, 2)->comment('kg');
            $table->decimal('tinggi_badan', 5, 2)->comment('cm');
            $table->decimal('lingkar_kepala', 5, 2)->nullable()->comment('cm');
            $table->decimal('lingkar_lengan', 5, 2)->nullable()->comment('cm');
            $table->enum('status_gizi', ['normal', 'stunting', 'wasting', 'underweight'])->default('normal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertumbuhan');
    }
};
