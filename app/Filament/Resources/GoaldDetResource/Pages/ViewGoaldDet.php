<?php

namespace App\Filament\Resources\GoaldDetResource\Pages;

use App\Filament\Resources\GoaldDetResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGoaldDet extends ViewRecord
{
    protected static string $resource = GoaldDetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
