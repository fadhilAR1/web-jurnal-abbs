<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing foreign key constraint
        Schema::table('jurnal_kbm', function (Blueprint $table) {
            $table->dropForeign(['mapel_id']);
        });

        // Recreate with cascading delete
        Schema::table('jurnal_kbm', function (Blueprint $table) {
            $table->foreign('mapel_id')
                ->references('id')
                ->on('master_mapels')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('jurnal_kbm', function (Blueprint $table) {
            $table->dropForeign(['mapel_id']);
        });

        Schema::table('jurnal_kbm', function (Blueprint $table) {
            $table->foreign('mapel_id')->references('id')->on('master_mapels');
        });
    }
};
