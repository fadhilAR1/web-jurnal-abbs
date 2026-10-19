<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiswaEkstrakurikulerTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('siswa_ekstrakurikuler')) {
            Schema::create('siswa_ekstrakurikuler', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ekstrakurikuler_id');
                $table->unsignedBigInteger('siswa_id');
                $table->timestamps();

                // add FKs only if referenced tables exist
                if (Schema::hasTable('master_ekskul')) {
                    $table->foreign('ekstrakurikuler_id')->references('id')->on('master_ekskul')->onDelete('cascade');
                }
                if (Schema::hasTable('siswas')) {
                    $table->foreign('siswa_id')->references('id')->on('siswas')->onDelete('cascade');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('siswa_ekstrakurikuler');
    }
}
