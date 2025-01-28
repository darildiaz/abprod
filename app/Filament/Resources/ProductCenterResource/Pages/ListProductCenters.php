<?php

namespace App\Filament\Resources\ProductCenterResource\Pages;

use App\Filament\Resources\ProductCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductCenters extends ListRecords
{
    protected static string $resource = ProductCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
