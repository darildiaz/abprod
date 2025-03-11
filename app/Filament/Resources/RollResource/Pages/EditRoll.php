<?php

namespace App\Filament\Resources\RollResource\Pages;

use App\Filament\Resources\RollResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoll extends EditRecord
{
    protected static string $resource = RollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
