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
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        $karyawans = Karyawan::all();
        if ($karyawans->isEmpty()) {
            return;
        }

        $budi = $karyawans->get(0);
        $siti = $karyawans->get(1);
        $ahmad = $karyawans->get(2);

        $records = [
            // Kemarin - Budi (Reguler)
            [
                'karyawan_id' => $budi->id,
                'tanggal' => $yesterday,
                'waktu_masuk' => "{$yesterday} 08:00:00",
                'waktu_pulang' => "{$yesterday} 17:00:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ],
            // Hari Ini - Budi (Absensi Pertama: Reguler)
            [
                'karyawan_id' => $budi->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 07:55:00",
                'waktu_pulang' => "{$today} 17:00:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ],
            // Hari Ini - Budi (Absensi Kedua di hari yang sama: Lembur / Overtime)
            [
                'karyawan_id' => $budi->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 18:00:00",
                'waktu_pulang' => "{$today} 21:00:00",
                'is_overtime' => true,
                'alasan_overtime' => 'Deploy rilis modul absensi & overtime ke server produksi',
            ],
            // Hari Ini - Siti (Absensi Pertama: Reguler)
            [
                'karyawan_id' => $siti->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 08:02:00",
                'waktu_pulang' => "{$today} 17:05:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ],
            // Hari Ini - Siti (Absensi Kedua: Lembur)
            [
                'karyawan_id' => $siti->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 17:30:00",
                'waktu_pulang' => "{$today} 20:30:00",
                'is_overtime' => true,
                'alasan_overtime' => 'Penyusunan laporan bulanan payroll dan insentif lembur tim',
            ],
            // Hari Ini - Ahmad (Hanya Reguler)
            [
                'karyawan_id' => $ahmad->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 08:10:00",
                'waktu_pulang' => "{$today} 17:15:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ],
        ];

        foreach ($records as $item) {
            Absensi::firstOrCreate(
                [
                    'karyawan_id' => $item['karyawan_id'],
                    'tanggal' => $item['tanggal'],
                    'waktu_masuk' => $item['waktu_masuk'],
                ],
                $item
            );
        }
    }
}
