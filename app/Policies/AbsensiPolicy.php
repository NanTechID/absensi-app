<?php

namespace App\Policies;

use App\Models\Absensi;
use App\Models\User;
use App\Services\RoleHierarchy;
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
        return $user->can('View:Absensi') || $user->can('ViewAny:Absensi');
    }

    public function view(User $user, Absensi $absensi): bool
    {
        if (
            ($user->karyawan_id && $absensi->karyawan_id === $user->karyawan_id)
            || $absensi->user_id === $user->id
        ) {
            return $user->can('View:Absensi');
        }

        return $user->can('ViewAny:Absensi')
            && $absensi->karyawan !== null
            && RoleHierarchy::canViewKaryawan($user, $absensi->karyawan);
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Absensi');
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
