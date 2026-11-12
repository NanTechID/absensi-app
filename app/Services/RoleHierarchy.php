<?php

namespace App\Services;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class RoleHierarchy
{
    private const HIERARCHY = [
        'team_member',
        'group_leader',
        'section_leader',
        'supervisor',
        'assistant_manager',
        'team_manager',
        'departement_manager',
    ];

    private const POSITION_NAMES = [
        'team_member' => 'Team Member',
        'group_leader' => 'Group Leader',
        'section_leader' => 'Section Leader',
        'supervisor' => 'Supervisor',
        'assistant_manager' => 'Assistant Manager',
        'team_manager' => 'Team Manager',
        'departement_manager' => 'Departement Manager',
    ];

    public static function roles(): array
    {
        return [...self::HIERARCHY, 'admin'];
    }

    /**
     * Roles below the given role that it may view, not including its own role.
     */
    public static function accessibleRoles(string $roleName): array
    {
        $roleName = self::normalizeRole($roleName);
        $rank = array_search($roleName, self::HIERARCHY, true);

        if ($rank === false) {
            return [];
        }

        return array_slice(self::HIERARCHY, 0, $rank);
    }

    public static function roleForPosition(?string $position): ?string
    {
        if (! $position) {
            return null;
        }

        $normalized = self::normalizeRole($position);

        return in_array($normalized, self::HIERARCHY, true) ? $normalized : null;
    }

    public static function positionNames(array $roles): array
    {
        return array_values(array_filter(array_map(
            fn (string $role): ?string => self::POSITION_NAMES[self::normalizeRole($role)] ?? null,
            $roles,
        )));
    }

    public static function primaryRole(User $user): ?string
    {
        $roles = $user->getRoleNames()->all();

        if (in_array('admin', $roles, true)) {
            return 'admin';
        }

        foreach (array_reverse(self::HIERARCHY) as $role) {
            if (in_array($role, $roles, true)) {
                return $role;
            }
        }

        return null;
    }

    /**
     * Apply the attendance visibility rules to a query.
     */
    public static function scopeAbsensis(Builder $query, User $user): Builder
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        $role = self::primaryRole($user);
        $employee = $user->karyawan;
        $visiblePositions = $user->can('ViewAny:Absensi')
            ? self::positionNames($role ? self::accessibleRoles($role) : [])
            : [];

        return $query->where(function (Builder $visible) use ($user, $employee, $role, $visiblePositions): void {
            $visible->where('user_id', $user->id);

            if ($user->karyawan_id) {
                $visible->orWhere('karyawan_id', $user->karyawan_id);
            }

            if (! $employee || $visiblePositions === []) {
                return;
            }

            $visible->orWhereHas('karyawan', function (Builder $subordinates) use ($employee, $role, $visiblePositions): void {
                $subordinates->whereIn('jabatan', $visiblePositions);

                if ($role === 'departement_manager') {
                    $departmentId = $employee->team?->departemen_id;

                    if ($departmentId === null) {
                        $subordinates->whereRaw('1 = 0');

                        return;
                    }

                    $subordinates->whereHas('team', fn (Builder $team) => $team->where('departemen_id', $departmentId));

                    return;
                }

                if ($employee->team_id === null) {
                    $subordinates->whereRaw('1 = 0');

                    return;
                }

                $subordinates->where('team_id', $employee->team_id);
            });
        });
    }

    public static function canViewKaryawan(User $user, Karyawan $karyawan): bool
    {
        if ($user->hasRole('admin') || ($user->karyawan_id && $user->karyawan_id === $karyawan->id)) {
            return true;
        }

        $role = self::primaryRole($user);

        if (! $role || ! in_array(self::roleForPosition($karyawan->jabatan), self::accessibleRoles($role), true)) {
            return false;
        }

        $employee = $user->karyawan;

        if (! $employee) {
            return false;
        }

        if ($role === 'departement_manager') {
            $departmentId = $employee->team?->departemen_id;

            return $departmentId !== null && $karyawan->team?->departemen_id === $departmentId;
        }

        return $employee->team_id !== null && $employee->team_id === $karyawan->team_id;
    }

    private static function normalizeRole(string $role): string
    {
        $normalized = strtolower(trim($role));
        $normalized = preg_replace('/[\s-]+/', '_', $normalized) ?? $normalized;

        return $normalized === 'department_manager' ? 'departement_manager' : $normalized;
    }
}
