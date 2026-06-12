<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('url', 500);
            $table->string('page_title', 100)->nullable();
            $table->string('method', 10)->default('GET');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->timestamp('visited_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'visited_at']);
            $table->index('visited_at');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
