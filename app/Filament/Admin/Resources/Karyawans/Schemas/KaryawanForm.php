<?php

namespace App\Filament\Admin\Resources\Karyawans\Schemas;

use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Team;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class KaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_karyawan')
                    ->label('Nama Karyawan')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                Select::make('jabatan')
                    ->label('Jabatan')
                    ->options([
                        'Team Member'         => 'Team Member',
                        'Group Leader'        => 'Group Leader',
                        'Section Leader'      => 'Section Leader',
                        'Supervisor'          => 'Supervisor',
                        'Assistant Manager'   => 'Assistant Manager',
                        'Team Manager'        => 'Team Manager',
                        'Deputi Manager'      => 'Deputi Manager',
                        'Departement Manager' => 'Departement Manager',
                        'Presiden Director'   => 'Presiden Director',
                    ])
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('departemen_selector')
                    ->label('Departemen')
                    ->options(fn () => Departemen::query()->orderBy('nama_departemen')->pluck('nama_departemen', 'id'))
                    ->live()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->dehydrated(false)
                    ->afterStateHydrated(function (Select $component, ?Karyawan $record) {
                        if ($record && $record->team) {
                            $component->state($record->team->departemen_id);
                        }
                    })
                    ->required(),
                Select::make('team_id')
                    ->label('Team')
                    ->options(function (Get $get) {
                        $depId = $get('departemen_selector');
                        if (!$depId) return [];
                        return Team::where('departemen_id', $depId)->orderBy('nama_team')->pluck('nama_team', 'id');
                    })
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->columns(2);
    }
}
