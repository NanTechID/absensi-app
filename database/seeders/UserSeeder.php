<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'position' => 'admin',
            ]
        );
        $admin->position = 'admin';
        $admin->save();
        $admin->syncRoles(['admin']);

        // 2. Akun pengguna untuk setiap karyawan
        $karyawans = Karyawan::all();
        $positionMap = [
            'Team Member' => 'team_member',
            'Group Leader' => 'group_leader',
            'Section Leader' => 'section_leader',
            'Supervisor' => 'supervisor',
            'Assistant Manager' => 'assistant_manager',
            'Team Manager' => 'team_manager',
            'Departement Manager' => 'departement_manager',
            'Presiden Director' => 'admin',
        ];

        foreach ($karyawans as $k) {
            $roleName = $positionMap[$k->jabatan] ?? 'team_member';
            $user = User::firstOrCreate(
                ['email' => $k->email],
                [
                    'name' => $k->nama_karyawan,
                    'password' => Hash::make('password'),
                    'position' => $roleName,
                    'karyawan_id' => $k->id,
                ]
            );

            $user->karyawan_id = $k->id;
            $user->position = $roleName;
            $user->save();
            $user->syncRoles([$roleName]);
        }
    }
}
