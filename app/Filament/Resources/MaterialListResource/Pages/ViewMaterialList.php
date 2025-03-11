<?php

namespace App\Filament\Resources\MaterialListResource\Pages;

use App\Filament\Resources\MaterialListResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMaterialList extends ViewRecord
{
    protected static string $resource = MaterialListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
