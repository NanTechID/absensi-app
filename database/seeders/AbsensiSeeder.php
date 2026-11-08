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

        $budi = $karyawans->firstWhere('nama_karyawan', 'Budi Santoso');
        $siti = $karyawans->firstWhere('nama_karyawan', 'Siti Rahmawati');
        $ahmad = $karyawans->firstWhere('nama_karyawan', 'Ahmad Fauzi');
        $rian = $karyawans->firstWhere('nama_karyawan', 'Rian Pratama');
        $dewi = $karyawans->firstWhere('nama_karyawan', 'Dewi Lestari');

        $records = [];

        // Budi Santoso (Admin / Presiden Director)
        if ($budi) {
            $records[] = [
                'karyawan_id' => $budi->id,
                'tanggal' => $yesterday,
                'waktu_masuk' => "{$yesterday} 08:00:00",
                'waktu_pulang' => "{$yesterday} 17:00:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ];
            $records[] = [
                'karyawan_id' => $budi->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 07:55:00",
                'waktu_pulang' => "{$today} 17:00:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ];
            $records[] = [
                'karyawan_id' => $budi->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 18:00:00",
                'waktu_pulang' => "{$today} 21:00:00",
                'is_overtime' => true,
                'alasan_overtime' => 'Deploy rilis modul absensi & overtime ke server produksi',
            ];
        }

        // Siti Rahmawati (Departement Manager)
        if ($siti) {
            $records[] = [
                'karyawan_id' => $siti->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 08:02:00",
                'waktu_pulang' => "{$today} 17:05:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ];
            $records[] = [
                'karyawan_id' => $siti->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 17:30:00",
                'waktu_pulang' => "{$today} 20:30:00",
                'is_overtime' => true,
                'alasan_overtime' => 'Penyusunan laporan bulanan payroll dan insentif lembur tim',
            ];
        }

        // Ahmad Fauzi (Supervisor)
        if ($ahmad) {
            $records[] = [
                'karyawan_id' => $ahmad->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 08:10:00",
                'waktu_pulang' => "{$today} 17:15:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ];
        }

        // Dewi Lestari (Section Leader)
        if ($dewi) {
            $records[] = [
                'karyawan_id' => $dewi->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 07:50:00",
                'waktu_pulang' => "{$today} 17:00:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ];
        }

        // Rian Pratama (Team Member)
        if ($rian) {
            $records[] = [
                'karyawan_id' => $rian->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 07:45:00",
                'waktu_pulang' => "{$today} 16:45:00",
                'is_overtime' => false,
                'alasan_overtime' => null,
            ];
            $records[] = [
                'karyawan_id' => $rian->id,
                'tanggal' => $today,
                'waktu_masuk' => "{$today} 17:15:00",
                'waktu_pulang' => "{$today} 19:30:00",
                'is_overtime' => true,
                'alasan_overtime' => 'Lembur pengerjaan tugas modul Android dan API Absensi',
            ];
        }

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
