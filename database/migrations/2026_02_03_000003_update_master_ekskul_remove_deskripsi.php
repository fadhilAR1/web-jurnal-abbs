<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('master_ekskul') && Schema::hasColumn('master_ekskul', 'deskripsi')) {
            Schema::table('master_ekskul', function (Blueprint $table) {
                $table->dropColumn('deskripsi');
            });
        }

        // If the table doesn't exist, create it (safeguard for environments using migrations)
        if (!Schema::hasTable('master_ekskul')) {
            Schema::create('master_ekskul', function (Blueprint $table) {
                $table->id();
                $table->string('nama_ekskul');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('master_ekskul') && !Schema::hasColumn('master_ekskul', 'deskripsi')) {
            Schema::table('master_ekskul', function (Blueprint $table) {
                $table->text('deskripsi')->nullable()->after('nama_ekskul');
            });
        }
    }
};