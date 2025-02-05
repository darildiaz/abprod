<?php

namespace App\Filament\Resources\ProductiondetResource\Pages;

use App\Filament\Resources\ProductiondetResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductiondet extends ViewRecord
{
    protected static string $resource = ProductiondetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
