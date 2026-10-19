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
        Schema::table('siswas', function (Blueprint $table) {
            if (!Schema::hasColumn('siswas', 'kelas_id')) {
                $table->foreignId('kelas_id')->nullable()->constrained('master_kelas');
            }
            if (!Schema::hasColumn('siswas', 'nama_siswa')) {
                $table->string('nama_siswa')->nullable();
            }
            if (!Schema::hasColumn('siswas', 'jenis_kelamin')) {
                $table->string('jenis_kelamin')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            //
        });
    }
};
