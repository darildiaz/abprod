<?php

namespace App\Filament\Resources\ProductiondetResource\Pages;

use App\Filament\Resources\ProductiondetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductiondets extends ListRecords
{
    protected static string $resource = ProductiondetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
