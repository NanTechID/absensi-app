<?php

namespace App\Filament\Admin\Resources\Departemens\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DepartemenForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_departemen')
                    ->required()
                    ->label('Nama Departemen')
                    ->maxLength(40),
            ]);
    }
}
