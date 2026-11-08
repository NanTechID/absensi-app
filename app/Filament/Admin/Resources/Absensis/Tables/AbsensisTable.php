<?php

namespace App\Filament\Admin\Resources\Absensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama_karyawan')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->label('Tanggal')
                    ->sortable(),
                TextColumn::make('waktu_masuk')
                    ->dateTime('d M Y H:i')
                    ->label('Masuk')
                    ->sortable(),
                TextColumn::make('waktu_pulang')
                    ->dateTime('d M Y H:i')
                    ->label('Pulang')
                    ->sortable(),
                ImageColumn::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->height(60),
                ImageColumn::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->height(60),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Sakit' => 'danger',
                        'Cuti' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Cuti' => 'Cuti',
                    ]),
                SelectFilter::make('karyawan_id')
                    ->relationship('karyawan', 'nama_karyawan')
                    ->label('Karyawan')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
