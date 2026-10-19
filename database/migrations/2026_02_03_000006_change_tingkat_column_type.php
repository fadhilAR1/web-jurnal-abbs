<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('master_kelas') && Schema::hasColumn('master_kelas', 'tingkat')) {
            try {
                Schema::table('master_kelas', function (Blueprint $table) {
                    $table->string('tingkat')->change();
                });
            } catch (\Throwable $e) {
                // If doctrine/dbal is missing, log and skip. The developer should install doctrine/dbal before running this migration.
                \Log::warning('Could not change master_kelas.tingkat to string: ' . $e->getMessage());
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_kelas') && Schema::hasColumn('master_kelas', 'tingkat')) {
            try {
                Schema::table('master_kelas', function (Blueprint $table) {
                    $table->integer('tingkat')->change();
                });
            } catch (\Throwable $e) {
                \Log::warning('Could not revert master_kelas.tingkat to integer: ' . $e->getMessage());
            }
        }
    }
};