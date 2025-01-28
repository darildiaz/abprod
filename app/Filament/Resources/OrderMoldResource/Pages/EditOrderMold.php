<?php

namespace App\Filament\Resources\OrderMoldResource\Pages;

use App\Filament\Resources\OrderMoldResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrderMold extends EditRecord
{
    protected static string $resource = OrderMoldResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
