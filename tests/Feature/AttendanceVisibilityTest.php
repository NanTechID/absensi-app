<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Team;
use App\Models\User;
use App\Services\RoleHierarchy;
use App\Filament\Admin\Resources\Absensis\AbsensiResource;
use Database\Seeders\RolePermissionSeeder;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_leader_sees_own_and_lower_roles_in_the_same_team(): void
    {
        $department = Departemen::create(['nama_departemen' => 'Engineering']);
        $team = Team::create(['nama_team' => 'Mobile', 'departemen_id' => $department->id]);
        $otherTeam = Team::create(['nama_team' => 'Web', 'departemen_id' => $department->id]);

        $leader = $this->userWithRole('group_leader', $team);
        $member = $this->userWithRole('team_member', $team);
        $otherMember = $this->userWithRole('team_member', $otherTeam);
        $otherLeader = $this->userWithRole('group_leader', $team);

        $leaderAttendance = $this->attendanceFor($leader);
        $memberAttendance = $this->attendanceFor($member);
        $otherMemberAttendance = $this->attendanceFor($otherMember);
        $otherLeaderAttendance = $this->attendanceFor($otherLeader);

        $visibleIds = RoleHierarchy::scopeAbsensis(Absensi::query(), $leader)->pluck('id')->all();

        $this->assertEqualsCanonicalizing(
            [$leaderAttendance->id, $memberAttendance->id],
            $visibleIds,
        );
        $this->assertSame(
            [$memberAttendance->id],
            RoleHierarchy::scopeAbsensis(Absensi::query(), $member)->pluck('id')->all(),
        );
        $this->assertTrue($this->policy()->view($leader, $memberAttendance));
        $this->assertFalse($this->policy()->view($leader, $otherMemberAttendance));
        $this->assertFalse($this->policy()->view($leader, $otherLeaderAttendance));

        $leader->revokePermissionTo('ViewAny:Absensi');
        $this->assertSame(
            [$leaderAttendance->id],
            RoleHierarchy::scopeAbsensis(Absensi::query(), $leader)->pluck('id')->all(),
        );
        $this->assertFalse($this->policy()->view($leader, $memberAttendance));
    }

    public function test_department_manager_sees_lower_roles_in_the_department_and_admin_sees_everything(): void
    {
        $department = Departemen::create(['nama_departemen' => 'Operations']);
        $team = Team::create(['nama_team' => 'Operations A', 'departemen_id' => $department->id]);
        $sameDepartmentTeam = Team::create(['nama_team' => 'Operations B', 'departemen_id' => $department->id]);
        $otherDepartment = Departemen::create(['nama_departemen' => 'Finance']);
        $otherDepartmentTeam = Team::create(['nama_team' => 'Finance A', 'departemen_id' => $otherDepartment->id]);

        $manager = $this->userWithRole('departement_manager', $team);
        $teamManager = $this->userWithRole('team_manager', $sameDepartmentTeam);
        $member = $this->userWithRole('team_member', $otherDepartmentTeam);
        $admin = $this->userWithRole('admin');

        $managerAttendance = $this->attendanceFor($manager);
        $teamManagerAttendance = $this->attendanceFor($teamManager);
        $memberAttendance = $this->attendanceFor($member);

        $managerVisibleIds = RoleHierarchy::scopeAbsensis(Absensi::query(), $manager)->pluck('id')->all();
        $adminVisibleIds = RoleHierarchy::scopeAbsensis(Absensi::query(), $admin)->pluck('id')->all();

        $this->assertEqualsCanonicalizing(
            [$managerAttendance->id, $teamManagerAttendance->id],
            $managerVisibleIds,
        );
        $this->assertEqualsCanonicalizing(
            [$managerAttendance->id, $teamManagerAttendance->id, $memberAttendance->id],
            $adminVisibleIds,
        );
    }

    public function test_login_response_redirects_users_to_their_role_home(): void
    {
        $department = Departemen::create(['nama_departemen' => 'Product']);
        $team = Team::create(['nama_team' => 'Product A', 'departemen_id' => $department->id]);
        $member = $this->userWithRole('team_member', $team);
        $admin = $this->userWithRole('admin');

        $memberRequest = Request::create('/admin/login');
        $memberRequest->setUserResolver(fn () => $member);
        $memberResponse = app(LoginResponse::class)->toResponse($memberRequest);

        $this->assertSame(
            AbsensiResource::getUrl('index', panel: 'admin'),
            $memberResponse->getTargetUrl(),
        );

        $adminRequest = Request::create('/admin/login');
        $adminRequest->setUserResolver(fn () => $admin);
        $adminResponse = app(LoginResponse::class)->toResponse($adminRequest);

        $this->assertSame(route('filament.admin.pages.dashboard'), $adminResponse->getTargetUrl());
    }

    public function test_panel_exposes_attendance_to_members_and_role_management_only_to_admins(): void
    {
        $department = Departemen::create(['nama_departemen' => 'Quality']);
        $team = Team::create(['nama_team' => 'Quality A', 'departemen_id' => $department->id]);
        $member = $this->userWithRole('team_member', $team);
        $admin = $this->userWithRole('admin');
        $this->attendanceFor($member);
        $this->assertDatabaseCount('absensis', 1);

        $this->actingAs($member)
            ->get('/admin/absensis')
            ->assertOk();

        $this->actingAs($member)
            ->get('/admin/absensis/create')
            ->assertOk();

        $this->actingAs($member)
            ->get('/admin/karyawans')
            ->assertForbidden();

        $this->actingAs($member)
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($member)
            ->get('/admin/shield/roles')
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/admin/shield/roles')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/admin/users/create')
            ->assertOk();

        $this->actingAs($member)
            ->get('/admin/laporan-absensi')
            ->assertForbidden();

        $exportPeriod = [
            'startDate' => now()->startOfMonth()->toDateString(),
            'endDate' => now()->toDateString(),
        ];

        $this->actingAs($member)
            ->get(route('admin.reports.attendance.export', ['format' => 'xlsx', ...$exportPeriod]))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get('/admin/laporan-absensi')
            ->assertOk()
            ->assertSee('Laporan Absensi')
            ->assertSee('Ekspor')
            ->assertSee('Karyawan')
            ->assertSee('Tanggal')
            ->assertSee('Masuk')
            ->assertSee('Pulang')
            ->assertSee('Lembur')
            ->assertSee('Quality A');

        $excelResponse = $this->actingAs($admin)
            ->get(route('admin.reports.attendance.export', ['format' => 'xlsx', ...$exportPeriod]))
            ->assertDownload('laporan-absensi-' . $exportPeriod['startDate'] . '-sampai-' . $exportPeriod['endDate'] . '.xlsx');

        $reader = new \OpenSpout\Reader\XLSX\Reader();
        $reader->open($excelResponse->baseResponse->getFile()->getPathname());
        $exportedRows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $exportedRows[] = $row->toArray();
            }
        }

        $reader->close();

        $this->assertSame(['Karyawan', 'Departemen', 'Team', 'Tanggal', 'Masuk', 'Pulang', 'Lembur'], $exportedRows[0]);
        $this->assertSame('Quality A', $exportedRows[1][2]);

        $pdfResponse = $this->actingAs($admin)
            ->get(route('admin.reports.attendance.export', ['format' => 'pdf', ...$exportPeriod]))
            ->assertDownload('laporan-absensi-' . $exportPeriod['startDate'] . '-sampai-' . $exportPeriod['endDate'] . '.pdf');

        $this->assertStringStartsWith('%PDF-', $pdfResponse->getContent());
    }

    public function test_edit_forms_are_prefilled_and_saving_returns_to_the_resource_list(): void
    {
        $department = Departemen::create(['nama_departemen' => 'Quality Assurance']);
        $team = Team::create(['nama_team' => 'Automation', 'departemen_id' => $department->id]);
        $employee = Karyawan::create([
            'nama_karyawan' => 'Rina Tester',
            'email' => 'rina.tester@example.test',
            'jabatan' => 'Team Member',
            'team_id' => $team->id,
        ]);
        $admin = $this->userWithRole('admin');
        $admin->update(['position' => 'admin']);
        $attendance = Absensi::create([
            'user_id' => $admin->id,
            'karyawan_id' => $employee->id,
            'tanggal' => now()->toDateString(),
            'waktu_masuk' => '08:30',
            'waktu_pulang' => '17:00',
        ]);

        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        Livewire::test(\App\Filament\Admin\Resources\Departemens\Pages\EditDepartemen::class, ['record' => $department->id])
            ->assertSet('data.nama_departemen', 'Quality Assurance')
            ->fillForm(['nama_departemen' => 'Quality Assurance Updated'])
            ->call('save')
            ->assertRedirect(\App\Filament\Admin\Resources\Departemens\DepartemenResource::getUrl('index', panel: 'admin'));

        Livewire::test(\App\Filament\Admin\Resources\Teams\Pages\EditTeam::class, ['record' => $team->id])
            ->assertSet('data.nama_team', 'Automation')
            ->assertSet('data.departemen_id', $department->id)
            ->call('save')
            ->assertRedirect(\App\Filament\Admin\Resources\Teams\TeamResource::getUrl('index', panel: 'admin'));

        Livewire::test(\App\Filament\Admin\Resources\Karyawans\Pages\EditKaryawan::class, ['record' => $employee->id])
            ->assertSet('data.nama_karyawan', 'Rina Tester')
            ->assertSet('data.email', 'rina.tester@example.test')
            ->assertSet('data.departemen_selector', $department->id)
            ->assertSet('data.team_id', $team->id)
            ->call('save')
            ->assertRedirect(\App\Filament\Admin\Resources\Karyawans\KaryawanResource::getUrl('index', panel: 'admin'));

        Livewire::test(\App\Filament\Admin\Resources\Absensis\Pages\EditAbsensi::class, ['record' => $attendance->id])
            ->assertSet('data.karyawan_id', $employee->id)
            ->assertSet('data.tanggal', now()->toDateString())
            ->assertSet('data.waktu_masuk', fn ($value): bool => str_contains((string) $value, '08:30'))
            ->assertSet('data.waktu_pulang', fn ($value): bool => str_contains((string) $value, '17:00'))
            ->call('save')
            ->assertRedirect(\App\Filament\Admin\Resources\Absensis\AbsensiResource::getUrl('index', panel: 'admin'));

        Livewire::test(\App\Filament\Admin\Resources\Users\Pages\EditUser::class, ['record' => $admin->id])
            ->assertSet('data.name', $admin->name)
            ->assertSet('data.email', $admin->email)
            ->assertSet('data.position', 'admin')
            ->call('save')
            ->assertRedirect(\App\Filament\Admin\Resources\Users\UserResource::getUrl('index', panel: 'admin'));
    }

    public function test_user_position_changes_keep_the_spatie_role_in_sync(): void
    {
        Role::findOrCreate('team_member', 'web');
        Role::findOrCreate('supervisor', 'web');

        $user = User::factory()->create(['position' => 'team_member']);
        $this->assertSame(['team_member'], $user->getRoleNames()->all());

        $user->update(['position' => 'supervisor']);

        $this->assertSame(['supervisor'], $user->fresh()->getRoleNames()->all());
    }

    public function test_role_seeder_assigns_subordinate_permissions_by_hierarchy(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $this->assertTrue(Role::findByName('assistant_manager')->hasPermissionTo('ViewAny:Absensi'));
        $this->assertTrue(Role::findByName('departement_manager')->hasPermissionTo('ViewAny:Absensi'));
        $this->assertFalse(Role::findByName('team_member')->hasPermissionTo('ViewAny:Absensi'));
        $this->assertTrue(Role::findByName('admin')->hasPermissionTo('Delete:Absensi'));
    }

    public function test_legacy_attendance_permissions_are_mapped_to_shield_resource_permissions(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $role = Role::findByName('group_leader');
        $legacyPermissions = [
            'attendance.create',
            'attendance.view',
            'attendance.view_subordinates',
        ];

        foreach ($legacyPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role->syncPermissions($legacyPermissions);

        $migration = require database_path('migrations/2026_11_12_152400_map_attendance_permissions_to_shield_resource.php');
        $migration->up();

        $role = $role->fresh();
        $this->assertTrue($role->hasPermissionTo('Create:Absensi'));
        $this->assertTrue($role->hasPermissionTo('View:Absensi'));
        $this->assertTrue($role->hasPermissionTo('ViewAny:Absensi'));
        $this->assertDatabaseMissing('permissions', ['name' => 'attendance.create', 'guard_name' => 'web']);
        $this->assertDatabaseMissing('permissions', ['name' => 'attendance.view', 'guard_name' => 'web']);
        $this->assertDatabaseMissing('permissions', ['name' => 'attendance.view_subordinates', 'guard_name' => 'web']);
    }

    public function test_legacy_permission_rows_are_renamed_when_shield_permissions_do_not_exist_yet(): void
    {
        $role = Role::findOrCreate('group_leader', 'web');
        $legacyPermissions = ['attendance.create', 'attendance.view'];

        foreach ($legacyPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role->syncPermissions($legacyPermissions);

        $migration = require database_path('migrations/2026_11_12_152400_map_attendance_permissions_to_shield_resource.php');
        $migration->up();

        $this->assertTrue($role->fresh()->hasPermissionTo('Create:Absensi'));
        $this->assertTrue($role->fresh()->hasPermissionTo('View:Absensi'));
        $this->assertDatabaseMissing('permissions', ['name' => 'attendance.create', 'guard_name' => 'web']);
        $this->assertDatabaseMissing('permissions', ['name' => 'attendance.view', 'guard_name' => 'web']);
    }

    public function test_role_view_shows_assigned_resource_permissions_as_selected(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = $this->userWithRole('admin');
        $admin->update(['position' => 'admin']);
        $role = Role::findByName('team_member');
        $role->syncPermissions(['Create:Absensi', 'View:Absensi']);

        $this->actingAs($admin);
        Filament::setCurrentPanel('admin');

        Livewire::test(\BezhanSalleh\FilamentShield\Resources\Roles\Pages\ViewRole::class, ['record' => $role->id])
            ->assertSet('data', fn (array $data): bool => collect($data[\App\Filament\Admin\Resources\Absensis\AbsensiResource::class] ?? [])
                ->sort()
                ->values()
                ->all() === ['Create:Absensi', 'View:Absensi']);

        $this->get("/admin/shield/roles/{$role->id}")
            ->assertOk()
            ->assertSee('Absensi')
            ->assertDontSee('Custom Permissions');
    }

    private function userWithRole(string $roleName, ?Team $team = null): User
    {
        $user = User::factory()->create();

        Role::findOrCreate($roleName, 'web');
        $user->assignRole($roleName);

        if ($team) {
            $employee = Karyawan::create([
                'nama_karyawan' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'jabatan' => RoleHierarchy::positionNames([$roleName])[0],
                'team_id' => $team->id,
            ]);

            $user->karyawan()->associate($employee);
            $user->save();
        }

        if ($roleName !== 'team_member' && $roleName !== 'admin') {
            $permission = Permission::findOrCreate('ViewAny:Absensi', 'web');
            $user->givePermissionTo($permission);
        }

        if ($roleName !== 'admin') {
            $user->givePermissionTo([
                Permission::findOrCreate('Create:Absensi', 'web'),
                Permission::findOrCreate('View:Absensi', 'web'),
            ]);
        }

        return $user;
    }

    private function attendanceFor(User $user): Absensi
    {
        return Absensi::create([
            'user_id' => $user->id,
            'karyawan_id' => $user->karyawan_id,
            'tanggal' => now()->toDateString(),
        ]);
    }

    private function policy(): \App\Policies\AbsensiPolicy
    {
        return app(\App\Policies\AbsensiPolicy::class);
    }
}
