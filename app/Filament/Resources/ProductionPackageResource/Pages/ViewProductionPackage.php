<?php

namespace App\Filament\Resources\ProductionPackageResource\Pages;

use App\Filament\Resources\ProductionPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductionPackage extends ViewRecord
{
    protected static string $resource = ProductionPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
