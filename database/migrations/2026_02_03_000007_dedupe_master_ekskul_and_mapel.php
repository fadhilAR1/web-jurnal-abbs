<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // normalize and dedupe master_ekskul
        if (Schema::hasTable('master_ekskul')) {
            DB::statement("UPDATE master_ekskul SET nama_ekskul = TRIM(nama_ekskul)");

            $groups = DB::select("SELECT LOWER(nama_ekskul) AS nm, GROUP_CONCAT(id) AS ids, MIN(id) AS keep_id, COUNT(*) AS cnt FROM master_ekskul GROUP BY LOWER(nama_ekskul) HAVING cnt > 1");

            foreach ($groups as $g) {
                $ids = explode(',', $g->ids);
                $keep = (int) $g->keep_id;
                foreach ($ids as $id) {
                    $id = (int) $id;
                    if ($id === $keep) continue;

                    // move references
                    if (Schema::hasTable('jurnal_ekskul')) {
                        DB::table('jurnal_ekskul')->where('ekskul_id', $id)->update(['ekskul_id' => $keep]);
                    }
                    if (Schema::hasTable('siswa_ekstrakurikuler')) {
                        DB::table('siswa_ekstrakurikuler')->where('ekstrakurikuler_id', $id)->update(['ekstrakurikuler_id' => $keep]);
                    }

                    // delete duplicate
                    DB::table('master_ekskul')->where('id', $id)->delete();
                }
            }

            // try to add unique constraint on nama_ekskul (ignore if exists)
            try {
                Schema::table('master_ekskul', function (Blueprint $table) {
                    $table->unique('nama_ekskul');
                });
            } catch (\Exception $e) {
                // ignore (index may already exist)
            }
        }

        // normalize and dedupe master_mapels (grouping by nama_mapel + program)
        if (Schema::hasTable('master_mapels')) {
            DB::statement("UPDATE master_mapels SET nama_mapel = TRIM(nama_mapel), program = COALESCE(program, 'Semua')");

            $groups = DB::select("SELECT LOWER(nama_mapel) AS nm, program, GROUP_CONCAT(id) AS ids, MIN(id) AS keep_id, COUNT(*) AS cnt FROM master_mapels GROUP BY LOWER(nama_mapel), program HAVING cnt > 1");

            foreach ($groups as $g) {
                $ids = explode(',', $g->ids);
                $keep = (int) $g->keep_id;
                foreach ($ids as $id) {
                    $id = (int) $id;
                    if ($id === $keep) continue;

                    // move references
                    if (Schema::hasTable('jurnal_kbm')) {
                        DB::table('jurnal_kbm')->where('mapel_id', $id)->update(['mapel_id' => $keep]);
                    }

                    // delete duplicate
                    DB::table('master_mapels')->where('id', $id)->delete();
                }
            }

            // try to add unique constraint on nama_mapel + program
            try {
                Schema::table('master_mapels', function (Blueprint $table) {
                    $table->unique(['nama_mapel', 'program']);
                });
            } catch (\Exception $e) {
                // ignore
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_ekskul')) {
            Schema::table('master_ekskul', function (Blueprint $table) {
                try { $table->dropUnique('master_ekskul_nama_ekskul_unique'); } catch (\Exception $e) {}
            });
        }
        if (Schema::hasTable('master_mapels')) {
            Schema::table('master_mapels', function (Blueprint $table) {
                try { $table->dropUnique('master_mapels_nama_mapel_program_unique'); } catch (\Exception $e) {}
            });
        }
    }
};
