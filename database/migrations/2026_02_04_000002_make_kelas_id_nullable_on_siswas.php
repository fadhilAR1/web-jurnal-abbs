<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('siswas')) {
            return;
        }

        // Attempt to drop foreign key first if exists, then change column to nullable and re-add FK with ON DELETE SET NULL
        Schema::table('siswas', function (Blueprint $table) {
            // drop foreign key if exists
            try {
                $table->dropForeign(['kelas_id']);
            } catch (\Exception $e) {
                // ignore
            }
        });

        // Change column to nullable using raw statement (safer if doctrine/dbal not installed)
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `siswas` MODIFY `kelas_id` BIGINT UNSIGNED NULL');
        } else {
            // Fallback to the change() method (requires doctrine/dbal)
            Schema::table('siswas', function (Blueprint $table) {
                $table->unsignedBigInteger('kelas_id')->nullable()->change();
            });
        }

        // Re-add foreign key with ON DELETE SET NULL
        Schema::table('siswas', function (Blueprint $table) {
            $table->foreign('kelas_id')->references('id')->on('master_kelas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('siswas')) {
            return;
        }

        Schema::table('siswas', function (Blueprint $table) {
            try {
                $table->dropForeign(['kelas_id']);
            } catch (\Exception $e) {
                // ignore
            }
        });

        // Make the column NOT NULL again (set default 0 first to avoid failure)
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `siswas` MODIFY `kelas_id` BIGINT UNSIGNED NOT NULL');
        } else {
            Schema::table('siswas', function (Blueprint $table) {
                $table->unsignedBigInteger('kelas_id')->nullable(false)->change();
            });
        }

        // Re-add FK without SET NULL behavior
        Schema::table('siswas', function (Blueprint $table) {
            $table->foreign('kelas_id')->references('id')->on('master_kelas');
        });
    }
};