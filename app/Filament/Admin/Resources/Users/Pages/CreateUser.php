<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['position'] === 'admin') {
            $data['karyawan_id'] = null;
        } elseif (empty($data['karyawan_id'])) {
            throw ValidationException::withMessages([
                'data.karyawan_id' => 'Pilih data karyawan untuk akun non-admin.',
            ]);
        }

        return $data;
    }
}
