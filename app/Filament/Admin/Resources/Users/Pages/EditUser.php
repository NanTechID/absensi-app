<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
