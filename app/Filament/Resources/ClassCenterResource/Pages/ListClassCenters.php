<?php

namespace App\Filament\Resources\ClassCenterResource\Pages;

use App\Filament\Resources\ClassCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassCenters extends ListRecords
{
    protected static string $resource = ClassCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
