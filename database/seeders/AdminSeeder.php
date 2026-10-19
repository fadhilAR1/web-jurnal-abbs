<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key constraints temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate users table
        DB::table('users')->truncate();
        
        // Enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Create admin user with encrypted password
        User::create([
            'name' => 'Admin ABBS Surakarta',
            'username' => 'adminabbssurakarta',
            'password' => Hash::make('loginwebabbs0'),
            'role' => 'admin',
        ]);
    }
}