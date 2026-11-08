<?php

namespace App\Filament\Admin\Resources\Absensis\Schemas;

use App\Models\Absensi;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('karyawan_id')
                    ->label('Nama Karyawan')
                    ->relationship('karyawan', 'nama_karyawan')
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->default(now()->toDateString())
                    ->required()
                    ->rules([
                        fn (Get $get, ?Absensi $record) => function (string $attribute, $value, Closure $fail) use ($get, $record) {
                            $karyawanId = $get('karyawan_id');
                            if (! $karyawanId || ! $value) {
                                return;
                            }

                            $query = Absensi::where('karyawan_id', $karyawanId)
                                ->whereDate('tanggal', $value);

                            if ($record) {
                                $query->where('id', '!=', $record->id);
                            }

                            if ($query->exists()) {
                                $fail('Karyawan sudah melakukan absensi pada tanggal tersebut.');
                            }
                        },
                    ]),

                DateTimePicker::make('waktu_masuk')
                    ->label('Waktu Masuk')
                    ->seconds(false)
                    ->default(now()),

                FileUpload::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->directory('foto_absensi')
                    ->image()
                    ->imageEditor()
                    ->nullable(),

                DateTimePicker::make('waktu_pulang')
                    ->label('Waktu Pulang')
                    ->seconds(false)
                    ->nullable(),

                FileUpload::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->directory('foto_absensi')
                    ->image()
                    ->imageEditor()
                    ->nullable(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Hadir' => 'Hadir',
                        'Izin' => 'Izin',
                        'Sakit' => 'Sakit',
                        'Cuti' => 'Cuti',
                    ])
                    ->default('Hadir')
                    ->required(),
            ])
            ->columns(2);
    }
}
