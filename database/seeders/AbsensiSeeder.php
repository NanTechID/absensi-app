<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $karyawans = Karyawan::all();
        if ($karyawans->isEmpty()) {
            return;
        }

        $records = [
            // Kemarin
            [
                'karyawan' => $karyawans->get(0),
                'tanggal' => $yesterday->toDateString(),
                'waktu_masuk' => $yesterday->copy()->setHour(7)->setMinute(55),
                'waktu_pulang' => $yesterday->copy()->setHour(17)->setMinute(5),
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'Hadir',
            ],
            [
                'karyawan' => $karyawans->get(1),
                'tanggal' => $yesterday->toDateString(),
                'waktu_masuk' => $yesterday->copy()->setHour(8)->setMinute(0),
                'waktu_pulang' => $yesterday->copy()->setHour(17)->setMinute(0),
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'Hadir',
            ],
            [
                'karyawan' => $karyawans->get(2),
                'tanggal' => $yesterday->toDateString(),
                'waktu_masuk' => $yesterday->copy()->setHour(8)->setMinute(10),
                'waktu_pulang' => $yesterday->copy()->setHour(17)->setMinute(30),
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'Hadir',
            ],
            // Hari Ini
            [
                'karyawan' => $karyawans->get(0),
                'tanggal' => $today->toDateString(),
                'waktu_masuk' => $today->copy()->setHour(7)->setMinute(50),
                'waktu_pulang' => $today->copy()->setHour(17)->setMinute(0),
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'Hadir',
            ],
            [
                'karyawan' => $karyawans->get(1),
                'tanggal' => $today->toDateString(),
                'waktu_masuk' => $today->copy()->setHour(8)->setMinute(5),
                'waktu_pulang' => null,
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'Hadir',
            ],
            [
                'karyawan' => $karyawans->get(2),
                'tanggal' => $today->toDateString(),
                'waktu_masuk' => null,
                'waktu_pulang' => null,
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'Izin',
            ],
            [
                'karyawan' => $karyawans->get(3),
                'tanggal' => $today->toDateString(),
                'waktu_masuk' => null,
                'waktu_pulang' => null,
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'Sakit',
            ],
            [
                'karyawan' => $karyawans->get(4),
                'tanggal' => $today->toDateString(),
                'waktu_masuk' => null,
                'waktu_pulang' => null,
                'foto_masuk' => null,
                'foto_pulang' => null,
                'status' => 'Cuti',
            ],
        ];

        foreach ($records as $item) {
            if (! $item['karyawan']) {
                continue;
            }

            Absensi::firstOrCreate(
                [
                    'karyawan_id' => $item['karyawan']->id,
                    'tanggal' => $item['tanggal'],
                ],
                [
                    'waktu_masuk' => $item['waktu_masuk'],
                    'waktu_pulang' => $item['waktu_pulang'],
                    'foto_masuk' => $item['foto_masuk'],
                    'foto_pulang' => $item['foto_pulang'],
                    'status' => $item['status'],
                ]
            );
        }
    }
}
