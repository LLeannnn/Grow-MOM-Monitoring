<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email');
            $table->tinyInteger('rating')->default(5)->comment('1-5');
            $table->string('kategori')->default('umum');
            $table->text('pesan');
            $table->enum('status', ['pending', 'dibaca'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
