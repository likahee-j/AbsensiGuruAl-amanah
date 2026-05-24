<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Akun awal Kepala Sekolah. Data sekolah, tahun pelajaran, guru, libur,
        // dan absensi akan diisi sendiri lewat panel admin.
        User::updateOrCreate(
            ['email' => 'alamanah@gmail.com'],
            [
                'name' => 'Kepala Sekolah',
                'username' => 'alamanah',
                'password' => Hash::make('12345678'),
                'role' => 'kepsek',
                'email_verified_at' => now(),
            ]
        );
    }
}
