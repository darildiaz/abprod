<?php

namespace App\Filament\Resources\OrderReferenceResource\Pages;

use App\Filament\Resources\OrderReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderReferences extends ListRecords
{
    protected static string $resource = OrderReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
