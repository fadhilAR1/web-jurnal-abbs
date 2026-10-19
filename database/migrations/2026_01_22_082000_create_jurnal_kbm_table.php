<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jurnal_kbm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users');
            $table->foreignId('kelas_id')->constrained('master_kelas');
            $table->foreignId('mapel_id')->constrained('master_mapel');
            $table->date('tanggal');
            $table->string('materi');
            $table->json('absensi'); // {siswa_id: status}
            $table->text('catatan')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jurnal_kbm');
    }
};