<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@lab.sch.id',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Ditian Pratama',
            'email'    => 'ditian@lab.sch.id',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Petugas Lab',
            'email'    => 'petugas@lab.sch.id',
            'password' => bcrypt('password'),
            'role'     => 'petugas',
        ]);

        $userSiswa = User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi@siswa.sch.id',
            'password' => bcrypt('password'),
            'role'     => 'siswa',
        ]);

        Siswa::create([
            'nama'    => 'Budi Santoso',
            'nis'     => '2024001',
            'kelas'   => 'XII IPA 1',
            'id_user' => $userSiswa->id,
        ]);
    }
}