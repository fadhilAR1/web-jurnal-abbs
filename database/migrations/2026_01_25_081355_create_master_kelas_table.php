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
        if (!Schema::hasTable('master_kelas')) {
            Schema::create('master_kelas', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kelas');
                $table->integer('tingkat');
                $table->string('jurusan')->default('IPA');
                $table->string('program')->nullable()->comment('Cambridge, ICT, Tahfidz');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kelas');
    }
};
