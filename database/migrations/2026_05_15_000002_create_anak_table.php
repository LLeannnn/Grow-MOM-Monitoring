<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ibu_id')->constrained('ibu')->onDelete('cascade');
            $table->string('nama_anak');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->decimal('berat_lahir', 5, 2)->nullable()->comment('kg');
            $table->decimal('panjang_lahir', 5, 2)->nullable()->comment('cm');
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anak');
    }
};
