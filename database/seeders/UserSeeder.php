<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name'      => 'Admin',
            'username'  => 'admin',
            'password'  => bcrypt('admin'),
            'role'      => 'ADMIN',
            'email'     => 'admin@admin.com',
            'npm'       => null,
        ]);


        // User::create([
        //     'name'      => 'Mahasiswa 1',
        //     'username'  => '10050021234',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs1@univ.ac.id',
        //     'npm'       => '10050021234',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 2',
        //     'username'  => '10050021235',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs2@univ.ac.id',
        //     'npm'       => '10050021235',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 3',
        //     'username'  => '10050021236',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs3@univ.ac.id',
        //     'npm'       => '10050021236',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 4',
        //     'username'  => '10050021237',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs4@univ.ac.id',
        //     'npm'       => '10050021237',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 5',
        //     'username'  => '10050021238',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs5@univ.ac.id',
        //     'npm'       => '10050021238',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 6',
        //     'username'  => '10050021239',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs6@univ.ac.id',
        //     'npm'       => '10050021239',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 7',
        //     'username'  => '10050021240',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs7@univ.ac.id',
        //     'npm'       => '10050021240',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 8',
        //     'username'  => '10050021241',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs8@univ.ac.id',
        //     'npm'       => '10050021241',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 9',
        //     'username'  => '10050021242',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs9@univ.ac.id',
        //     'npm'       => '10050021242',
        // ]);

        // User::create([
        //     'name'      => 'Mahasiswa 10',
        //     'username'  => '10050021243',
        //     'password'  => bcrypt('mahasiswa'),
        //     'role'      => 'USER',
        //     'email'     => 'mhs10@univ.ac.id',
        //     'npm'       => '10050021243',
        // ]);
    }
}
