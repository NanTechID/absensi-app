<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions untuk resource Absensi / Attendance
        $permissions = [
            'attendance.create',
            'attendance.view',              // view own records
            'attendance.view_subordinates', // melihat daftar absensi bawahan
            'attendance.update',
            'attendance.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Roles
        $roles = [
            'admin',
            'team_member',
            'group_leader',
            'section_leader',
            'supervisor',
            'assistant_manager',
            'team_manager',
            'departement_manager',
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
        }

        // Assign permissions:
        // Admin => semua permissions
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->syncPermissions($permissions);

        // Non-admin: create & view own
        $basicPerms = ['attendance.create', 'attendance.view'];
        $nonAdmins = [
            'team_member',
            'group_leader',
            'section_leader',
            'supervisor',
            'assistant_manager',
            'team_manager',
            'departement_manager',
        ];
        foreach ($nonAdmins as $r) {
            Role::where('name', $r)->first()->syncPermissions($basicPerms);
        }

        // Leaders & Managers: izin melihat bawahan
        $seeSubordinates = [
            'group_leader',
            'section_leader',
            'supervisor',
            'assistant_manager',
            'team_manager',
            'departement_manager',
        ];
        foreach ($seeSubordinates as $r) {
            Role::where('name', $r)->first()->givePermissionTo('attendance.view_subordinates');
        }

        // Sinkronisasi admin user
        $adminUser = User::where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            $adminUser->position = 'admin';
            $adminUser->save();
            $adminUser->syncRoles(['admin']);
        }
    }
}
