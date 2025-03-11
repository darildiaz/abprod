<?php

namespace App\Filament\Resources\RollProdtResource\Pages;

use App\Filament\Resources\RollProdtResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRollProdt extends ViewRecord
{
    protected static string $resource = RollProdtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
