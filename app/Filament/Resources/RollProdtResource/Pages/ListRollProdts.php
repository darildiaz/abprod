<?php

namespace App\Filament\Resources\RollProdtResource\Pages;

use App\Filament\Resources\RollProdtResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRollProdts extends ListRecords
{
    protected static string $resource = RollProdtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
