<?php

namespace App\Filament\Resources\ClassCenterResource\Pages;

use App\Filament\Resources\ClassCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassCenter extends EditRecord
{
    protected static string $resource = ClassCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
