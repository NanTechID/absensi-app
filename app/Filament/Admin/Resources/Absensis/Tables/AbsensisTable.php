<?php

namespace App\Filament\Admin\Resources\Absensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama_karyawan')
                    ->label('Karyawan')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('waktu_masuk')
                    ->label('Masuk')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('waktu_pulang')
                    ->label('Pulang')
                    ->time('H:i')
                    ->sortable(),
                IconColumn::make('is_overtime')
                    ->boolean()
                    ->label('Lembur')
                    ->sortable(),
                TextColumn::make('alasan_overtime')
                    ->label('Alasan Overtime')
                    ->toggleable()
                    ->limit(30),
                ImageColumn::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->height(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->height(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Challenge #1: Filter hanya data lembur (is_overtime = true)
                Filter::make('is_overtime')
                    ->label('Hanya Data Lembur')
                    ->query(fn (Builder $query): Builder => $query->where('is_overtime', true)),
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
