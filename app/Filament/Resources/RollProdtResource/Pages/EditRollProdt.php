<?php

namespace App\Filament\Resources\RollProdtResource\Pages;

use App\Filament\Resources\RollProdtResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRollProdt extends EditRecord
{
    protected static string $resource = RollProdtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
