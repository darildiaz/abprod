<?php

namespace App\Filament\Resources\OrderItemProductResource\Pages;

use App\Filament\Resources\OrderItemProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderItemProduct extends EditRecord
{
    protected static string $resource = OrderItemProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
