<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus email column
            if (Schema::hasColumn('users', 'email')) {
                $table->dropColumn('email');
            }
            
            // Hapus email_verified_at column
            if (Schema::hasColumn('users', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            
            // Hapus is_approved column
            if (Schema::hasColumn('users', 'is_approved')) {
                $table->dropColumn('is_approved');
            }
            
            // Hapus profile_completed column
            if (Schema::hasColumn('users', 'profile_completed')) {
                $table->dropColumn('profile_completed');
            }
            
            // Hapus remember_token column
            if (Schema::hasColumn('users', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('profile_completed')->default(false);
            $table->rememberToken();
        });
    }
};
