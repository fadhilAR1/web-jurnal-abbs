<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_piket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users');
            $table->string('hari');
            $table->date('tanggal');
            $table->json('guru_tidak_hadir')->nullable(); // [{nama: alasan}]
            $table->integer('jumlah_siswa_tidak_hadir')->default(0);
            $table->json('siswa_tidak_hadir_detail')->nullable();
            $table->json('tamu_sekolah')->nullable(); // [{nama: tujuan}]
            $table->json('kejadian_penting')->nullable(); // [{waktu: kejadian}]
            $table->text('catatan')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_piket');
    }
};