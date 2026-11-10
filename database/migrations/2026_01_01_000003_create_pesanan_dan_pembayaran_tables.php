<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('jas_id')->constrained('jas')->restrictOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pemesan', 100)->nullable();
            $table->string('nomor_hp', 20)->nullable();
            $table->date('tanggal_pesan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->date('tanggal_dikembalikan')->nullable();
            $table->string('ukuran', 10);
            $table->unsignedInteger('jumlah')->default(1);
            $table->unsignedBigInteger('total_harga');
            $table->enum('status', ['pending', 'menunggu_verifikasi', 'disewa', 'selesai', 'dibatalkan'])
                ->default('pending')
                ->index();
            $table->text('catatan')->nullable();
            $table->text('catatan_pengembalian')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('tanggal_pesan');
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->unique()->constrained('pesanan')->cascadeOnDelete();
            $table->enum('metode', ['qris', 'cash'])->default('qris');
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status', ['pending', 'diverifikasi', 'ditolak'])->default('pending')->index();
            $table->dateTime('tanggal_bayar')->nullable();
            $table->dateTime('tanggal_verifikasi')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('pesanan');
    }
};
