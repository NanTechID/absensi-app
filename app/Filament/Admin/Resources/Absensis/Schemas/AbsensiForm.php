<?php

namespace App\Filament\Admin\Resources\Absensis\Schemas;

use App\Models\Absensi;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama_karyawan')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $tanggal = $get('tanggal');
                        if ($state && $tanggal) {
                            $exists = Absensi::where('karyawan_id', $state)
                                ->whereDate('tanggal', $tanggal)
                                ->exists();
                            if ($exists) {
                                $set('is_overtime', true);
                            }
                        }
                    }),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->default(now()->toDateString())
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $karyawanId = $get('karyawan_id');
                        if ($karyawanId && $state) {
                            $exists = Absensi::where('karyawan_id', $karyawanId)
                                ->whereDate('tanggal', $state)
                                ->exists();
                            if ($exists) {
                                $set('is_overtime', true);
                            }
                        }
                    }),

                TimePicker::make('waktu_masuk')
                    ->label('Waktu Masuk')
                    ->required()
                    ->rules([
                        fn (Get $get, ?Absensi $record) => function (string $attribute, $value, Closure $fail) use ($get, $record) {
                            $karyawanId = $get('karyawan_id');
                            $tanggal = $get('tanggal');
                            if (! $karyawanId || ! $tanggal || ! $value) {
                                return;
                            }

                            // Challenge: validasi agar satu karyawan tidak bisa absen dua kali dengan waktu masuk yang sama
                            $query = Absensi::where('karyawan_id', $karyawanId)
                                ->whereDate('tanggal', $tanggal)
                                ->whereTime('waktu_masuk', $value);

                            if ($record) {
                                $query->where('id', '!=', $record->id);
                            }

                            if ($query->exists()) {
                                $fail('Karyawan sudah tercatat absen dengan waktu masuk yang sama pada tanggal ini.');
                            }
                        },
                    ]),

                TimePicker::make('waktu_pulang')
                    ->label('Waktu Pulang')
                    ->nullable(),

                FileUpload::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->directory('absensi/foto_masuk')
                    ->image(),

                FileUpload::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->directory('absensi/foto_pulang')
                    ->image(),

                Toggle::make('is_overtime')
                    ->label('Apakah Overtime?')
                    ->live(),

                Textarea::make('alasan_overtime')
                    ->label('Alasan Overtime')
                    ->visible(fn ($get) => (bool) $get('is_overtime'))
                    ->required(fn ($get) => (bool) $get('is_overtime')),
            ])
            ->columns(2);
    }
}
