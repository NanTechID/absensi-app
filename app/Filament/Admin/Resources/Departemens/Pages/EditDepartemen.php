<?php

namespace App\Filament\Admin\Resources\Departemens\Pages;

use App\Filament\Admin\Resources\Departemens\DepartemenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDepartemen extends EditRecord
{
    protected static string $resource = DepartemenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
