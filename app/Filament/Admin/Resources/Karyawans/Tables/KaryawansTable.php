<?php

namespace App\Filament\Admin\Resources\Karyawans\Tables;

use App\Models\Departemen;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class KaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_karyawan')->label('Nama Karyawan')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('jabatan')
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('team.nama_team')->label('Team')->sortable()->searchable(),
                TextColumn::make('team.departemen.nama_departemen')->label('Departemen')->sortable()->searchable(),
                TextColumn::make('absensis_count')
                    ->counts('absensis')
                    ->label('Total Absensi')
                    ->badge()
                    ->color('info')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('team_id')->label('Team')
                    ->relationship('team', 'nama_team'),
                SelectFilter::make('departemen_id')->label('Departemen')
                    ->options(fn () => Departemen::orderBy('nama_departemen')->pluck('nama_departemen', 'id')->toArray())
                    ->query(function ($query, $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('team', fn($q) => $q->where('departemen_id', $data['value']));
                        }
                    }),
                SelectFilter::make('jabatan')
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
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
