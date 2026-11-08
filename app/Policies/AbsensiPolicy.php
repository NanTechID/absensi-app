<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsensiPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        // Admin memiliki akses penuh untuk semua action
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('attendance.view') || $user->can('attendance.view_subordinates');
    }

    public function view(User $user, Absensi $absensi): bool
    {
        // Karyawan boleh melihat absensinya sendiri
        if ($user->karyawan_id && $absensi->karyawan_id === $user->karyawan_id) {
            return $user->can('attendance.view');
        }

        // Atasan dapat melihat absensi bawahan jika memiliki permission
        if ($user->can('attendance.view_subordinates')) {
            $role = $user->getRoleNames()->first();
            $allowed = match ($role) {
                'group_leader' => ['Team Member'],
                'section_leader' => ['Team Member', 'Group Leader'],
                'supervisor' => ['Team Member', 'Group Leader', 'Section Leader'],
                'assistant_manager' => ['Team Member', 'Group Leader', 'Section Leader', 'Supervisor'],
                'team_manager' => ['Team Member', 'Group Leader', 'Section Leader', 'Assistant Manager', 'Supervisor'],
                'departement_manager' => ['Team Member', 'Group Leader', 'Section Leader', 'Supervisor', 'Assistant Manager', 'Team Manager'],
                default => [],
            };

            return in_array($absensi->karyawan?->jabatan, $allowed, true);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->can('attendance.create');
    }

    public function update(User $user, Absensi $absensi): bool
    {
        return false; // Hanya admin yang diizinkan (dihandle oleh before())
    }

    public function delete(User $user, Absensi $absensi): bool
    {
        return false; // Hanya admin yang diizinkan (dihandle oleh before())
    }
}
