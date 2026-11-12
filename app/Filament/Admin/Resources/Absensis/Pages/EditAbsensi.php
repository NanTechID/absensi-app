<?php

namespace App\Filament\Admin\Resources\Absensis\Pages;

use App\Filament\Admin\Resources\Absensis\AbsensiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\User;

class EditAbsensi extends EditRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['user_id'] = User::where('karyawan_id', $data['karyawan_id'])->value('id');

        return $data;
    }
}
