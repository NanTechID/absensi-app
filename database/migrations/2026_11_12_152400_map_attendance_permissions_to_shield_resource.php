<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private const PERMISSION_MAP = [
        'attendance.view_subordinates' => 'ViewAny:Absensi',
        'attendance.view' => 'View:Absensi',
        'attendance.create' => 'Create:Absensi',
        'attendance.update' => 'Update:Absensi',
        'attendance.delete' => 'Delete:Absensi',
    ];

    public function up(): void
    {
        $this->mapPermissions(self::PERMISSION_MAP);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        $this->mapPermissions(array_flip(self::PERMISSION_MAP));
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function mapPermissions(array $permissionMap): void
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $permissionKey = $columnNames['permission_pivot_key'] ?? 'permission_id';

        foreach ($permissionMap as $sourceName => $targetName) {
            $sourcePermissions = DB::table($tableNames['permissions'])
                ->where('name', $sourceName)
                ->get();

            foreach ($sourcePermissions as $sourcePermission) {
                $targetPermission = DB::table($tableNames['permissions'])
                    ->where('name', $targetName)
                    ->where('guard_name', $sourcePermission->guard_name)
                    ->first();

                if (! $targetPermission) {
                    DB::table($tableNames['permissions'])
                        ->where('id', $sourcePermission->id)
                        ->update([
                            'name' => $targetName,
                            'updated_at' => now(),
                        ]);

                    continue;
                }

                $this->movePermissionAssignments(
                    $tableNames['role_has_permissions'],
                    $permissionKey,
                    $sourcePermission->id,
                    $targetPermission->id,
                );
                $this->movePermissionAssignments(
                    $tableNames['model_has_permissions'],
                    $permissionKey,
                    $sourcePermission->id,
                    $targetPermission->id,
                );

                DB::table($tableNames['permissions'])
                    ->where('id', $sourcePermission->id)
                    ->delete();
            }
        }
    }

    private function movePermissionAssignments(string $table, string $permissionKey, int $sourceId, int $targetId): void
    {
        $assignments = DB::table($table)
            ->where($permissionKey, $sourceId)
            ->get();

        foreach ($assignments as $assignment) {
            $values = (array) $assignment;
            $values[$permissionKey] = $targetId;

            DB::table($table)->insertOrIgnore($values);
        }

        DB::table($table)
            ->where($permissionKey, $sourceId)
            ->delete();
    }
};
