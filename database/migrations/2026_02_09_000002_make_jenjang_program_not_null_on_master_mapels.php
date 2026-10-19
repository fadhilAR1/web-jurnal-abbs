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
        if (Schema::hasTable('master_mapels')) {
            // Drop unique constraint first to avoid conflicts
            Schema::table('master_mapels', function (Blueprint $table) {
                try {
                    $table->dropUnique('master_mapels_nama_mapel_program_unique');
                } catch (\Exception $e) {
                    // Ignore if constraint doesn't exist
                }
            });

            // Set default values for any null entries
            \DB::table('master_mapels')
                ->whereNull('jenjang')
                ->update(['jenjang' => 'X']);
            
            \DB::table('master_mapels')
                ->whereNull('program')
                ->update(['program' => 'Cambridge']);
            
            // Delete duplicates - keep only the first one per nama_mapel+program+jenjang
            $duplicates = \DB::table('master_mapels')
                ->selectRaw('nama_mapel, program, jenjang, MIN(id) as keep_id')
                ->groupBy('nama_mapel', 'program', 'jenjang')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicates as $dup) {
                \DB::table('master_mapels')
                    ->where('nama_mapel', $dup->nama_mapel)
                    ->where('program', $dup->program)
                    ->where('jenjang', $dup->jenjang)
                    ->where('id', '!=', $dup->keep_id)
                    ->delete();
            }

            // Then modify the columns
            Schema::table('master_mapels', function (Blueprint $table) {
                $table->string('jenjang')->nullable(false)->change();
                $table->string('program')->nullable(false)->change();
                // Add unique constraint with all three columns
                $table->unique(['nama_mapel', 'program', 'jenjang']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_mapels')) {
            Schema::table('master_mapels', function (Blueprint $table) {
                try {
                    $table->dropUnique('master_mapels_nama_mapel_program_jenjang_unique');
                } catch (\Exception $e) {
                    // Ignore
                }
                $table->string('jenjang')->nullable()->change();
                $table->string('program')->nullable()->change();
            });
        }
    }
};
