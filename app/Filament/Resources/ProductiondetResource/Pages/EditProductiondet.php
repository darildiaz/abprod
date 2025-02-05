<?php

namespace App\Filament\Resources\ProductiondetResource\Pages;

use App\Filament\Resources\ProductiondetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductiondet extends EditRecord
{
    protected static string $resource = ProductiondetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
