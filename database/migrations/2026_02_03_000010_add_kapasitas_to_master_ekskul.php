<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('master_ekskul') && !Schema::hasColumn('master_ekskul', 'kapasitas')) {
            Schema::table('master_ekskul', function (Blueprint $table) {
                $table->unsignedInteger('kapasitas')->default(30)->after('nama_ekskul')->comment('Kapasitas peserta ekskul');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_ekskul') && Schema::hasColumn('master_ekskul', 'kapasitas')) {
            Schema::table('master_ekskul', function (Blueprint $table) {
                $table->dropColumn('kapasitas');
            });
        }
    }
};