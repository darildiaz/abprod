<?php

namespace App\Filament\Resources\ProductionPackageResource\Pages;

use App\Filament\Resources\ProductionPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductionPackages extends ListRecords
{
    protected static string $resource = ProductionPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
