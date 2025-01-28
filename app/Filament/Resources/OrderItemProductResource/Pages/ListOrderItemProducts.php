<?php

namespace App\Filament\Resources\OrderItemProductResource\Pages;

use App\Filament\Resources\OrderItemProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrderItemProducts extends ListRecords
{
    protected static string $resource = OrderItemProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
