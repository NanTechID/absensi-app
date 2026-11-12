<?php

namespace App\Filament\Admin\Resources\Absensis;

use App\Filament\Admin\Resources\Absensis\Pages\CreateAbsensi;
use App\Filament\Admin\Resources\Absensis\Pages\EditAbsensi;
use App\Filament\Admin\Resources\Absensis\Pages\ListAbsensis;
use App\Filament\Admin\Resources\Absensis\Schemas\AbsensiForm;
use App\Filament\Admin\Resources\Absensis\Tables\AbsensisTable;
use App\Models\Absensi;
use App\Services\RoleHierarchy;
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

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('viewAny', Absensi::class) ?? false;
    }

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
        $user = auth()->user();

        if (! $user) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        return RoleHierarchy::scopeAbsensis(parent::getEloquentQuery(), $user);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user !== null
            && $user->can('create', Absensi::class)
            && ($user->hasRole('admin') || $user->karyawan_id !== null);
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->can('view', $record) ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('update', $record) ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->can('delete', $record) ?? false;
    }

    public static function canDeleteAny(): bool
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
