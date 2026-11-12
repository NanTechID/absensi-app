<?php

namespace App\Filament\Admin\Resources\Departemens;

use App\Filament\Admin\Resources\Departemens\Pages;
use App\Filament\Admin\Resources\Departemens\Schemas\DepartemenForm;
use App\Filament\Admin\Resources\Departemens\Tables\DepartemensTable;
use App\Models\Departemen;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class DepartemenResource extends Resource
{
    protected static ?string $model = Departemen::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Departemen';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return DepartemenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DepartemensTable::configure($table);
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
            'index' => Pages\ListDepartemens::route('/'),
            'create' => Pages\CreateDepartemen::route('/create'),
            'edit' => Pages\EditDepartemen::route('/{record}/edit'),
        ];
    }
}
