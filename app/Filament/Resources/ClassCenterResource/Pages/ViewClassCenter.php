<?php

namespace App\Filament\Resources\ClassCenterResource\Pages;

use App\Filament\Resources\ClassCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewClassCenter extends ViewRecord
{
    protected static string $resource = ClassCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
