<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with 2 standard accounts.
     */
    public function run(): void
    {
        // 1. Akun Mahasiswa
        User::updateOrCreate(
            ['email' => 'mahasiswa@kampus.ac.id'],
            [
                'name' => 'Naufal Marzuki',
                'identifier' => '25170018',
                'role' => 'mahasiswa',
                'password' => Hash::make('mahasiswa123'),
            ]
        );

        // 2. Akun Admin
        User::updateOrCreate(
            ['email' => 'admin@kampus.ac.id'],
            [
                'name' => 'Admin Kemahasiswaan & Sarpras',
                'identifier' => 'admin',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
            ]
        );
    }
}
