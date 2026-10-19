<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('master_ekskul') && Schema::hasColumn('master_ekskul', 'pembina')) {
            Schema::table('master_ekskul', function (Blueprint $table) {
                if (Schema::hasColumn('master_ekskul', 'pembina')) {
                    $table->dropColumn('pembina');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_ekskul') && !Schema::hasColumn('master_ekskul', 'pembina')) {
            Schema::table('master_ekskul', function (Blueprint $table) {
                $table->string('pembina')->nullable()->after('nama_ekskul');
            });
        }
    }
};
