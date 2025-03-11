<?php

namespace App\Filament\Resources\SizeGroupResource\Pages;

use App\Filament\Resources\SizeGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSizeGroups extends ListRecords
{
    protected static string $resource = SizeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
