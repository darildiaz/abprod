<?php

namespace App\Filament\Resources\GoaldDetResource\Pages;

use App\Filament\Resources\GoaldDetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGoaldDets extends ListRecords
{
    protected static string $resource = GoaldDetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
