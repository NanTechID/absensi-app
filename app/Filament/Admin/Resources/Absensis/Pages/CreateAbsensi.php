<?php

namespace App\Filament\Admin\Resources\Absensis\Pages;

use App\Filament\Admin\Resources\Absensis\AbsensiResource;
use App\Models\Absensi;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class CreateAbsensi extends CreateRecord
{
    protected static string $resource = AbsensiResource::class;

    /**
     * Logika Overtime Otomatis sesuai materi Tugas.txt Section 6.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if (! $user) {
            throw new AuthorizationException;
        }

        if (! $user->hasRole('admin')) {
            if (! $user->karyawan_id) {
                throw new AuthorizationException('Akun ini tidak terhubung ke data karyawan.');
            }

            $data['karyawan_id'] = $user->karyawan_id;
            $data['user_id'] = $user->id;
        } else {
            $data['user_id'] = User::where('karyawan_id', $data['karyawan_id'])->value('id');
        }

        // Cek apakah karyawan sudah absen di tanggal ini
        $exists = Absensi::where('karyawan_id', $data['karyawan_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->exists();

        // Jika sudah ada absensi di hari ini → ini lembur (overtime)
        if ($exists) {
            $data['is_overtime'] = true;
        } else {
            $data['is_overtime'] = $data['is_overtime'] ?? false;
        }

        // Jika overtime tapi tidak isi alasan
        if (! empty($data['is_overtime']) && empty($data['alasan_overtime'])) {
            Notification::make()
                ->title('Alasan Lembur Wajib Diisi!')
                ->body('Karyawan sudah memiliki riwayat absensi pada tanggal ini, sehingga absensi ini dicatat sebagai lembur.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }

    /**
     * Challenge: Notifikasi sukses khusus saat absensi tersimpan.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Absensi Berhasil Disimpan!')
            ->success();
    }
}
