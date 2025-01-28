<?php

namespace App\Filament\Resources\OrderMoldResource\Pages;

use App\Filament\Resources\OrderMoldResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderMolds extends ListRecords
{
    protected static string $resource = OrderMoldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
