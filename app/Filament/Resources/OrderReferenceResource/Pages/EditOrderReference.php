<?php

namespace App\Filament\Resources\OrderReferenceResource\Pages;

use App\Filament\Resources\OrderReferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderReference extends EditRecord
{
    protected static string $resource = OrderReferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
