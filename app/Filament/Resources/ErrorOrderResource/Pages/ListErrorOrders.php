<?php

namespace App\Filament\Resources\ErrorOrderResource\Pages;

use App\Filament\Resources\ErrorOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListErrorOrders extends ListRecords
{
    protected static string $resource = ErrorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
