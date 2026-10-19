<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa');
            $table->enum('jenis_kelamin', ['L','P'])->nullable();
            $table->string('nis')->nullable()->unique();
            $table->unsignedBigInteger('kelas_id')->nullable();
            $table->timestamps();

            $table->foreign('kelas_id')->references('id')->on('master_kelas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('siswas');
    }
};
