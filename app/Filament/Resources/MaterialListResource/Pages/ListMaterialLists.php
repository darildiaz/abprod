<?php

namespace App\Filament\Resources\MaterialListResource\Pages;

use App\Filament\Resources\MaterialListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaterialLists extends ListRecords
{
    protected static string $resource = MaterialListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
