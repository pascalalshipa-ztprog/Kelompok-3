<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = collect(['Akademik', 'Fasilitas', 'Layanan', 'Kemahasiswaan'])
            ->mapWithKeys(fn (string $name): array => [
                $name => Category::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name],
                ),
            ]);

        $student = User::updateOrCreate(['email' => 'cantika@kampus.ac.id'], [
            'name' => 'Cantika Dwi Utari',
            'nim' => '25170023',
            'role' => 'student',
            'password' => 'password',
        ]);

        User::updateOrCreate(['email' => 'admin@kampus.ac.id'], [
            'name' => 'Admin Kampus',
            'nim' => null,
            'role' => 'admin',
            'password' => 'password',
        ]);

        foreach ([
            ['title' => 'Lampu koridor gedung B mati', 'category' => 'Fasilitas', 'description' => 'Koridor lantai dua gelap sejak minggu lalu.', 'status' => 'Diproses'],
            ['title' => 'Perbaikan data kartu mahasiswa', 'category' => 'Layanan', 'description' => 'Nama pada kartu mahasiswa perlu disesuaikan.', 'status' => 'Selesai'],
        ] as $sample) {
            $student->complaints()->firstOrCreate(
                ['title' => $sample['title']],
                [
                    'category_id' => $categories[$sample['category']]->id,
                    'description' => $sample['description'],
                    'status' => $sample['status'],
                ],
            );
        }

        $student->complaints()->firstOrCreate(
            ['title' => 'Jadwal konsultasi tugas akhir'],
            [
                'category_id' => $categories['Akademik']->id,
                'description' => 'Mohon informasi jadwal konsultasi dosen pembimbing.',
                'status' => 'Menunggu',
            ],
        );
    }
}
