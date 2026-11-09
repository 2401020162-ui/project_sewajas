<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimoni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jas_id')->constrained('jas')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('komentar');
            $table->timestamps();

            $table->unique(['user_id', 'jas_id']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimoni');
    }
};
