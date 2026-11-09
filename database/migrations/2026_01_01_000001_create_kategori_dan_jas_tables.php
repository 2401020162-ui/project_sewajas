<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->string('slug', 120)->unique();
            $table->timestamps();
        });

        Schema::create('jas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('nama', 120);
            $table->text('deskripsi')->nullable();
            $table->string('ukuran', 60)->default('S,M,L,XL');
            $table->unsignedInteger('harga_sewa');
            $table->unsignedInteger('stok')->default(0);
            $table->string('gambar')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index(['kategori_id', 'is_aktif']);
            $table->index('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jas');
        Schema::dropIfExists('kategori');
    }
};
