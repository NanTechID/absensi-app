<?php

namespace App\Filament\Admin\Resources\Absensis;

use App\Filament\Admin\Resources\Absensis\Pages\CreateAbsensi;
use App\Filament\Admin\Resources\Absensis\Pages\EditAbsensi;
use App\Filament\Admin\Resources\Absensis\Pages\ListAbsensis;
use App\Filament\Admin\Resources\Absensis\Schemas\AbsensiForm;
use App\Filament\Admin\Resources\Absensis\Tables\AbsensisTable;
use App\Models\Absensi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string | UnitEnum | null $navigationGroup = 'Data Absensi';

    protected static ?string $navigationLabel = 'Absensi';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return AbsensiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensisTable::configure($table);
    }

    /**
     * Membatasi query absensi sesuai hierarki jabatan (Core Logic Pertemuan 7).
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        // Admin melihat semua data absensi
        if ($user->hasRole('admin')) {
            return $query;
        }

        $role = $user->getRoleNames()->first();

        // Hierarki jabatan bawahan yang diizinkan dilihat oleh atasan
        $allowed = match ($role) {
            'group_leader' => ['Team Member'],
            'section_leader' => ['Team Member', 'Group Leader'],
            'supervisor' => ['Team Member', 'Group Leader', 'Section Leader'],
            'assistant_manager' => ['Team Member', 'Group Leader', 'Section Leader', 'Supervisor'],
            'team_manager' => ['Team Member', 'Group Leader', 'Section Leader', 'Assistant Manager', 'Supervisor'],
            'departement_manager' => ['Team Member', 'Group Leader', 'Section Leader', 'Supervisor', 'Assistant Manager', 'Team Manager'],
            default => [],
        };

        return $query->where(function (Builder $q) use ($user, $allowed) {
            // Selalu bisa melihat absensi milik sendiri
            if ($user->karyawan_id) {
                $q->where('karyawan_id', $user->karyawan_id);
            }

            // Atasan dapat melihat absensi bawahan sesuai daftar yang diizinkan
            if (! empty($allowed)) {
                $q->orWhereHas('karyawan', function (Builder $sub) use ($allowed) {
                    $sub->whereIn('jabatan', $allowed);
                });
            }
        });
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('attendance.create') ?? true;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAbsensis::route('/'),
            'create' => CreateAbsensi::route('/create'),
            'edit' => EditAbsensi::route('/{record}/edit'),
        ];
    }
}
