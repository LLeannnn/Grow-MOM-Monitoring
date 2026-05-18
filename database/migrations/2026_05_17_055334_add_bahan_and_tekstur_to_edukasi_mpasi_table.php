<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('edukasi_mpasi', function (Blueprint $table) {
            $table->text('bahan_makanan')->nullable()->after('kategori_usia');
            $table->text('tekstur_makanan')->nullable()->after('bahan_makanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('edukasi_mpasi', function (Blueprint $table) {
            $table->dropColumn(['bahan_makanan', 'tekstur_makanan']);
        });
    }
};
