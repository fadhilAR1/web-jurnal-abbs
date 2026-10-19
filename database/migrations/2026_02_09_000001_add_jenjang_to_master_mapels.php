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
        if (Schema::hasTable('master_mapels') && !Schema::hasColumn('master_mapels', 'jenjang')) {
            Schema::table('master_mapels', function (Blueprint $table) {
                $table->string('jenjang')->nullable()->comment('Jenjang: X, XI, XII');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_mapels') && Schema::hasColumn('master_mapels', 'jenjang')) {
            Schema::table('master_mapels', function (Blueprint $table) {
                $table->dropColumn('jenjang');
            });
        }
    }
};
