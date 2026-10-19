<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        // Sample guru users
        $gurus = [
            [
                'name' => 'Ibu Siti Nurhaliza',
                'username' => 'siti.nurhaliza',
                'password' => 'guru123',
            ],
            [
                'name' => 'Bapak Ahmad Hidayat',
                'username' => 'ahmad.hidayat',
                'password' => 'guru123',
            ],
            [
                'name' => 'Ibu Eka Dewi Susanti',
                'username' => 'eka.dewi',
                'password' => 'guru123',
            ],
        ];

        foreach ($gurus as $guru) {
            User::updateOrCreate(
                ['username' => $guru['username']],
                [
                    'name' => $guru['name'],
                    'password' => $guru['password'],
                    'role' => 'guru',
                ]
            );
        }
    }
}
