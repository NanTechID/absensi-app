<?php

namespace App\Filament\Admin\Resources\Teams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('departemen.nama_departemen')
                    ->label('Departemen')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nama_team')
                    ->label('Team')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat'),
            ])
            ->filters([
                SelectFilter::make('departemen_id')
                    ->relationship('departemen', 'nama_departemen')
                    ->label('Departemen'),
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
