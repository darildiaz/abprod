<?php

namespace App\Filament\Resources\RollResource\Pages;

use App\Filament\Resources\RollResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRolls extends ListRecords
{
    protected static string $resource = RollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
