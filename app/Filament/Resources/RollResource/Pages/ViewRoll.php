<?php

namespace App\Filament\Resources\RollResource\Pages;

use App\Filament\Resources\RollResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRoll extends ViewRecord
{
    protected static string $resource = RollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
