<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users');
            $table->string('jenis_kegiatan');
            $table->string('nama_kegiatan');
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->string('lokasi');
            $table->json('peserta'); // {siswa: jumlah, guru: jumlah}
            $table->text('deskripsi');
            $table->json('foto_kegiatan')->nullable();
            $table->json('dokumentasi')->nullable(); // File upload
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_kegiatan');
    }
};