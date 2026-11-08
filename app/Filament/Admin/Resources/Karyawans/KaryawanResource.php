<?php

namespace App\Filament\Admin\Resources\Karyawans;

use App\Filament\Admin\Resources\Karyawans\Pages;
use App\Filament\Admin\Resources\Karyawans\Schemas\KaryawanForm;
use App\Filament\Admin\Resources\Karyawans\Tables\KaryawansTable;
use App\Models\Karyawan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KaryawanResource extends Resource
{
    protected static ?string $model = Karyawan::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Karyawan';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return KaryawanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KaryawansTable::configure($table);
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
            'index' => Pages\ListKaryawans::route('/'),
            'create' => Pages\CreateKaryawan::route('/create'),
            'edit' => Pages\EditKaryawan::route('/{record}/edit'),
        ];
    }
}
