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
        if (Schema::hasTable('siswas')) {
            Schema::table('siswas', function (Blueprint $table) {
                if (Schema::hasColumn('siswas', 'nisn')) {
                    $table->dropColumn('nisn');
                }
            });

            Schema::table('siswas', function (Blueprint $table) {
                if (! Schema::hasColumn('siswas', 'nis')) {
                    $table->string('nis')->nullable()->after('nama_siswa');
                    $table->unique('nis');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('siswas')) {
            Schema::table('siswas', function (Blueprint $table) {
                if (Schema::hasColumn('siswas', 'nis')) {
                    $table->dropUnique(['nis']);
                    $table->dropColumn('nis');
                }
            });

            Schema::table('siswas', function (Blueprint $table) {
                if (! Schema::hasColumn('siswas', 'nisn')) {
                    $table->string('nisn')->nullable()->after('nama_siswa');
                }
            });
        }
    }
};
