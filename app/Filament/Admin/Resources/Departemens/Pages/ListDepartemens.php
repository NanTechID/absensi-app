<?php

namespace App\Filament\Admin\Resources\Departemens\Pages;

use App\Filament\Admin\Resources\Departemens\DepartemenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDepartemens extends ListRecords
{
    protected static string $resource = DepartemenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
