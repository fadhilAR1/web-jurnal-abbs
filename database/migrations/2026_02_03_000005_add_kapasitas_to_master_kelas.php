<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('master_kelas') && !Schema::hasColumn('master_kelas', 'kapasitas')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                $table->integer('kapasitas')->nullable()->default(32)->after('program')->comment('Jumlah maksimum siswa per kelas');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_kelas') && Schema::hasColumn('master_kelas', 'kapasitas')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                $table->dropColumn('kapasitas');
            });
        }
    }
};