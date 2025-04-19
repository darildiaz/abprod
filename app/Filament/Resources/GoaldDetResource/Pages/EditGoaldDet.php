<?php

namespace App\Filament\Resources\GoaldDetResource\Pages;

use App\Filament\Resources\GoaldDetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGoaldDet extends EditRecord
{
    protected static string $resource = GoaldDetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
