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

        // Shield-generated permissions for the attendance resource.
        $permissions = [
            'ViewAny:Absensi',
            'View:Absensi',
            'Create:Absensi',
            'Update:Absensi',
            'Delete:Absensi',
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

        // The admin role receives every attendance permission.
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->syncPermissions($permissions);

        // Basic roles can create attendance and view their own records.
        $basicPerms = ['Create:Absensi', 'View:Absensi'];
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

        // Leaders and managers can view subordinate records, still scoped by RoleHierarchy.
        $seeSubordinates = [
            'group_leader',
            'section_leader',
            'supervisor',
            'assistant_manager',
            'team_manager',
            'departement_manager',
        ];
        foreach ($seeSubordinates as $r) {
            Role::where('name', $r)->first()->givePermissionTo('ViewAny:Absensi');
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
