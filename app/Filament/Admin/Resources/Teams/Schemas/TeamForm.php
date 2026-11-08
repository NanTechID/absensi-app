<?php

namespace App\Filament\Admin\Resources\Teams\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('departemen_id')
                    ->relationship('departemen', 'nama_departemen')
                    ->label('Departemen')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nama_team')
                    ->label('Nama Team')
                    ->required()
                    ->maxLength(50),
            ]);
    }
}
