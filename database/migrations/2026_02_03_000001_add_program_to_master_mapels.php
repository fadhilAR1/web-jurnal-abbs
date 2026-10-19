<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('master_mapels') && !Schema::hasColumn('master_mapels', 'program')) {
            Schema::table('master_mapels', function (Blueprint $table) {
                $table->string('program')->nullable()->after('jenjang')->comment('Cambridge, ICT, Tahfidz, Semua');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_mapels') && Schema::hasColumn('master_mapels', 'program')) {
            Schema::table('master_mapels', function (Blueprint $table) {
                $table->dropColumn('program');
            });
        }
    }
};