<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('master_kelas')) {
            // If a foreign key exists on wali_kelas, drop it first using information_schema lookup
            $fk = \DB::selectOne("SELECT CONSTRAINT_NAME as name FROM information_schema.key_column_usage WHERE table_schema = DATABASE() AND table_name = 'master_kelas' AND column_name = 'wali_kelas'");
            if ($fk && isset($fk->name)) {
                try {
                    \DB::statement("ALTER TABLE master_kelas DROP FOREIGN KEY `{$fk->name}`");
                } catch (\Throwable $e) {
                    \Log::info('Could not drop foreign key (via statement) wali_kelas: ' . $e->getMessage());
                }
            }

            if (Schema::hasColumn('master_kelas', 'wali_kelas')) {
                Schema::table('master_kelas', function (Blueprint $table) {
                    $table->dropColumn('wali_kelas');
                });
            }

            if (!Schema::hasColumn('master_kelas', 'program')) {
                Schema::table('master_kelas', function (Blueprint $table) {
                    $table->string('program')->nullable()->after('jurusan')->comment('Cambridge, ICT, Tahfidz');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_kelas')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                if (!Schema::hasColumn('master_kelas', 'wali_kelas')) {
                    $table->foreignId('wali_kelas')->nullable()->constrained('users')->onDelete('set null')->after('jurusan');
                }
                if (Schema::hasColumn('master_kelas', 'program')) {
                    $table->dropColumn('program');
                }
            });
        }
    }
};