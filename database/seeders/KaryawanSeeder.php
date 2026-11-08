<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\Team;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mobileTeam = Team::where('nama_team', 'Mobile Development')->first();
        $webTeam = Team::where('nama_team', 'Web Development')->first();
        $hrTeam = Team::where('nama_team', 'Recruitment & Talent')->first();
        $financeTeam = Team::where('nama_team', 'Accounting')->first();

        $karyawans = [
            [
                'nama_karyawan' => 'Budi Santoso',
                'email' => 'budi.santoso@absensi.com',
                'jabatan' => 'Presiden Director',
                'team_id' => $webTeam?->id,
            ],
            [
                'nama_karyawan' => 'Siti Rahmawati',
                'email' => 'siti.rahmawati@absensi.com',
                'jabatan' => 'Departement Manager',
                'team_id' => $mobileTeam?->id,
            ],
            [
                'nama_karyawan' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@absensi.com',
                'jabatan' => 'Supervisor',
                'team_id' => $mobileTeam?->id,
            ],
            [
                'nama_karyawan' => 'Rian Pratama',
                'email' => 'rian.pratama@absensi.com',
                'jabatan' => 'Team Member',
                'team_id' => $mobileTeam?->id,
            ],
            [
                'nama_karyawan' => 'Dewi Lestari',
                'email' => 'dewi.lestari@absensi.com',
                'jabatan' => 'Section Leader',
                'team_id' => $webTeam?->id,
            ],
            [
                'nama_karyawan' => 'Fajar Nugroho',
                'email' => 'fajar.nugroho@absensi.com',
                'jabatan' => 'Team Member',
                'team_id' => $webTeam?->id,
            ],
            [
                'nama_karyawan' => 'Maya Anggraini',
                'email' => 'maya.anggraini@absensi.com',
                'jabatan' => 'Supervisor',
                'team_id' => $hrTeam?->id,
            ],
            [
                'nama_karyawan' => 'Bambang Tri',
                'email' => 'bambang.tri@absensi.com',
                'jabatan' => 'Team Member',
                'team_id' => $financeTeam?->id,
            ],
        ];

        foreach ($karyawans as $data) {
            if ($data['team_id']) {
                Karyawan::firstOrCreate(
                    ['email' => $data['email']],
                    $data
                );
            }
        }
    }
}

